<?php

namespace App\Http\Controllers\Payment\Deposit;

use App\{
    Models\User,
    Classes\Instamojo,
    Models\Deposit,
    Models\Transaction,
    Classes\GeniusMailer,
    Models\PaymentGateway
};

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class InstamojoController extends DepositBaseController
{

    public function store(Request $request){

        $data = PaymentGateway::whereKeyword('instamojo')->first();
        $user = $this->user;
        
        $item_amount = $request->amount;
        $curr = $this->curr;


        $supported_currency = json_decode($data->currency_id,true);
            if(!in_array($curr->id,$supported_currency)){
                return redirect()->back()->with('unsuccess',__('Invalid Currency For Instamojo Payment.'));
            }

        $cancel_url = route('deposit.payment.cancle');
        $notify_url = route('deposit.instamojo.notify');
        $item_name = "Deposit via Instamojo";

        $paydata = $data->convertAutoData();
        if($paydata['sandbox_check'] == 1){
        $api = new Instamojo($paydata['key'], $paydata['token'], 'https://test.instamojo.com/api/1.1/');
        }
        else {
        $api = new Instamojo($paydata['key'], $paydata['token']);
        }

        try {
            $response = $api->paymentRequestCreate(array(
            "purpose" => $item_name,
            "amount" => $item_amount,
            "send_email" => false,
            "email" => $user->email,
            "redirect_url" => $notify_url
        ));
                        
            $redirect_url = $response['longurl'];
            $dep['user_id'] = $user->id;
            $dep['currency'] = $this->curr->sign;
            $dep['currency_code'] = $this->curr->name;
            $dep['amount'] = $request->amount / $this->curr->value;
            $dep['currency_value'] = $this->curr->value;
            $dep['method'] = 'Instamojo';
            $dep['pay_id'] = $response['id'];

            Session::put('deposit',$dep);
                        
            $data['total'] =  $item_amount;
            $data['return_url'] = $notify_url;
            $data['cancel_url'] = $cancel_url;
            Session::put('paypal_items',$data);
            return redirect($redirect_url);
                                    
        }
        catch (Exception $e) {
            return redirect()->back()->with('unsuccess',$e->getMessage());
        }
 }

    
    public function notify(Request $request){

        $data = $request->all();

        $dep = Session::get('deposit');

        $success_url = route('deposit.payment.return');
        $cancel_url  = route('deposit.payment.cancle');


        if($dep['pay_id'] == $data['payment_request_id']){


                    $deposit = new Deposit;
                    $deposit->user_id = $dep['user_id'];
                    $deposit->currency = $dep['currency'];
                    $deposit->currency_code = $dep['currency_code'];
                    $deposit->amount = $dep['amount'];
                    $deposit->currency_value = $dep['currency_value'];
                    $deposit->method = $dep['method'];
                    $deposit->txnid = $dep['pay_id'];
                    $deposit->status = 1;
                    $deposit->save();

                    $user = \App\Models\User::findOrFail($deposit->user_id);
                    $user->balance = $user->balance + ($deposit->amount);
                    $user->save();

                    // store in transaction table
                    if ($deposit->status == 1) {
                        $transaction = new Transaction;
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

            return redirect($success_url);
        }
        else {
            return redirect($cancel_url);
        }

        return redirect($cancel_url);
    }

}