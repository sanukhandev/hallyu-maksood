<?php

namespace App\Http\Controllers\Payment\Deposit;

use App\{
    Models\Deposit,
    Classes\GeniusMailer,
    Models\PaymentGateway
};

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use PayPal\{
    Api\Item,
    Api\Payer,
    Api\Amount,
    Api\Payment,
    Api\ItemList,
    Rest\ApiContext,
    Api\Transaction,
    Api\RedirectUrls,
    Api\PaymentExecution,
    Auth\OAuthTokenCredential
};

use Redirect;
use Session;

class PaypalController extends DepositBaseController
{
    private $_api_context;
    public function __construct()
    {
        parent::__construct();
        $data = PaymentGateway::whereKeyword('paypal')->first();
        $paydata = $data->convertAutoData();
        $paypal_conf = \Config::get('paypal');
        $paypal_conf['client_id'] = $paydata['client_id'];
        $paypal_conf['secret'] = $paydata['client_secret'];
        $paypal_conf['settings']['mode'] = $paydata['sandbox_check'] == 1 ? 'sandbox' : 'live';
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function store(Request $request){

        $data = PaymentGateway::whereKeyword('paypal')->first();
        $user = $this->user;

       $item_amount = $request->amount;
        $curr = $this->curr;

        $supported_currency = json_decode($data->currency_id,true);
        if(!in_array($curr->id,$supported_currency)){
            return redirect()->back()->with('unsuccess',__('Invalid Currency For Paypal Payment.'));
        }

        $item_name = "Deposit via Paypal Payment";
        $cancel_url = route('deposit.payment.cancle');
        $notify_url = route('deposit.paypal.notify');

        $dep['user_id'] = $user->id;
        $dep['currency'] = $this->curr->sign;
        $dep['currency_code'] = $this->curr->name;
        $dep['amount'] = $request->amount / $this->curr->value;
        $dep['currency_value'] = $this->curr->value;
        $dep['method'] = 'Paypal';

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName('Deposit Amount') /** item name **/
            ->setCurrency($curr->name)
            ->setQuantity(1)
            ->setPrice($item_amount); /** unit price **/
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        $amount = new Amount();
        $amount->setCurrency($curr->name)
            ->setTotal($item_amount);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription($item_name);
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl($notify_url) /** Specify return URL **/
            ->setCancelUrl($cancel_url);
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/
        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            return redirect()->back()->with('unsuccess',$ex->getMessage());
        }
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                    break;
            }
        }
        /** add payment ID to session **/
        Session::put('deposit',$dep);
        Session::put('paypal_payment_id', $payment->getId());
        if (isset($redirect_url)) {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }
        return redirect()->back()->with('unsuccess',__('Unknown error occurred'));
        
    }

      public function notify(Request $request){

        $dep = Session::get('deposit');
        $success_url = route('deposit.payment.return');
        $cancel_url = route('deposit.payment.cancle');
        $input = $request->all();

        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');
        /** clear the session payment ID **/
        if (empty( $input['PayerID']) || empty( $input['token'])) {
            return redirect($cancel_url);
        } 
        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($input['PayerID']);
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);
        if ($result->getState() == 'approved') {
            $resp = json_decode($payment, true);

                $deposit = new Deposit;
                $deposit->user_id = $dep['user_id'];
                $deposit->currency = $dep['currency'];
                $deposit->currency_code = $dep['currency_code'];
                $deposit->amount = $dep['amount'];
                $deposit->currency_value = $dep['currency_value'];
                $deposit->method = $dep['method'];
                $deposit->txnid = $resp['transactions'][0]['related_resources'][0]['sale']['id'];
                $deposit->status = 1;
                $deposit->save();

                $user = \App\Models\User::findOrFail($deposit->user_id);
                $user->balance = $user->balance + ($deposit->amount);
                $user->save();

                // store in transaction table
                if ($deposit->status == 1) {
                    $transaction = new \App\Models\Transaction;
                    $transaction->txn_number = Str::random(3).substr(time(), 6,8).Str::random(3);
                    $transaction->user_id = $deposit->user_id;
                    $transaction->amount = $deposit->amount;
                    $transaction->user_id = $deposit->user_id;
                    $transaction->currency_sign = $deposit->currency;
                    $transaction->currency_code = $deposit->currency_code;
                    $transaction->currency_value= $deposit->currency_value;
                    $transaction->method = $deposit->method;
                    $transaction->txnid = $deposit->txnid;
                    $transaction->details = 'Payment Deposit';
                    $transaction->type = 'plus';
                    $transaction->save();
                }
            
                $maildata = [
                    'to' => $user->email,
                    'type' => "wallet_deposit",
                    'cname' => $user->name,
                    'damount' => $deposit->amount,
                    'wbalance' => $user->balance,
                    'oamount' => "",
                    'aname' => "",
                    'aemail' => "",
                    'onumber' => "",
                ];

                $mailer = new GeniusMailer();
                $mailer->sendAutoMail($maildata);

            Session::forget('deposit');
            Session::forget('paypal_payment_id');
            return redirect($success_url);
        }
        else {
            return redirect($cancel_url);
        }

    }
}