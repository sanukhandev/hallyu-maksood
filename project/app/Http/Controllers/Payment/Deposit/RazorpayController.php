<?php

namespace App\Http\Controllers\Payment\Deposit;

use App\{
    Models\Deposit,
    Models\Transaction,
    Classes\GeniusMailer,
    Models\PaymentGateway
};

use Illuminate\{
    Http\Request,
    Support\Facades\Session
};

use Razorpay\Api\Api;
use Illuminate\Support\Str;


class RazorpayController extends DepositBaseController
{

    public function __construct()
    {
        parent::__construct();
        $data = PaymentGateway::whereKeyword('razorpay')->first();
        $paydata = $data->convertAutoData();
        $this->keyId = $paydata['key'];
        $this->keySecret = $paydata['secret'];
        $this->displayCurrency = 'INR';
        $this->api = new Api($this->keyId, $this->keySecret);
    }

    public function store(Request $request){

        $data = PaymentGateway::whereKeyword('razorpay')->first();
        $user = $this->user;

        $item_amount = $request->amount;
        $curr = $this->curr;

        $this->displayCurrency = ''.$curr->name.'';


        $supported_currency = json_decode($data->currency_id,true);
        if(!in_array($curr->id,$supported_currency)){
            return redirect()->back()->with('unsuccess',__('Invalid Currency For Razorpay Payment.'));
        }

        $return_url = route('deposit.payment.return');
        $cancel_url = route('deposit.payment.cancle');
        $notify_url = route('deposit.razorpay.notify');
        $item_name = "Deposit via Razorpay";
        $item_number = $user->id;


        $orderData = [
            'receipt'         => $item_number,
            'amount'          => $item_amount * 100, // 2000 rupees in paise
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];
        
        $razorpayOrder = $this->api->order->create($orderData);
        
        $razorpayOrderId = $razorpayOrder['id'];
        
        session(['razorpay_order_id'=> $razorpayOrderId]);


    // Redirect to paypal IPN

                    $deposit = new Deposit;
                    $deposit->user_id = $user->id;
                    $deposit->currency = $this->curr->sign;
                    $deposit->currency_code = $this->curr->name;
                    $deposit->amount = $request->amount / $this->curr->value;
                    $deposit->currency_value = $this->curr->value;
                    $deposit->method = 'Razorpay';
                    $deposit->save();

                    $displayAmount = $amount = $orderData['amount'];
                    
                    if ($this->displayCurrency !== 'INR')
                    {
                        $url = "https://api.fixer.io/latest?symbols=$this->displayCurrency&base=INR";
                        $exchange = json_decode(file_get_contents($url), true);
                    
                        $displayAmount = $exchange['rates'][$this->displayCurrency] * $amount / 100;
                    }
                    
                    $checkout = 'automatic';
                    
                    if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
                    {
                        $checkout = $_GET['checkout'];
                    }
                    
                    $data = [
                        "key"               => $this->keyId,
                        "amount"            => $amount,
                        "name"              => $item_name,
                        "description"       => $item_name,
                        "prefill"           => [
                            "name"              => $user->name,
                            "email"             => $user->email,
                            "contact"           => $user->phone,
                        ],
                        "notes"             => [
                            "address"           => $user->address,
                            "merchant_order_id" => $item_number,
                        ],
                        "theme"             => [
                            "color"             => "{{$this->gs->colors}}"
                        ],
                        "order_id"          => $razorpayOrderId,
                    ];
                    
                    if ($this->displayCurrency !== 'INR')
                    {
                        $data['display_currency']  = $this->displayCurrency;
                        $data['display_amount']    = $displayAmount;
                    }
                    
                    $json = json_encode($data);
                    $displayCurrency = $this->displayCurrency;
                    Session::put('item_number',$item_number); 
                    
        return view( 'frontend.razorpay-checkout', compact( 'data','displayCurrency','json','notify_url' ) );

 }

    
    public function notify(Request $request){

            $success = true;

            
            if (empty($_POST['razorpay_payment_id']) === false)
            {
                try
                {

                    $attributes = array(
                        'razorpay_order_id' => session('razorpay_order_id'),
                        'razorpay_payment_id' => $_POST['razorpay_payment_id'],
                        'razorpay_signature' => $_POST['razorpay_signature']
                    );
            
                    $this->api->utility->verifyPaymentSignature($attributes);
                }
                catch(SignatureVerificationError $e)
                {
                    $success = false;
                    $error = 'Razorpay Error : ' . $e->getMessage();
                }
            }
            
            if ($success === true)
            {
                
                $transaction_id = $_POST['razorpay_payment_id'];

                $deposit = Deposit::where('user_id','=',Session::get('item_number'))->orderBy('created_at','desc')->first();
                $user = \App\Models\User::findOrFail($deposit->user_id);

                $user->balance = $user->balance + ($deposit->amount);
                $user->save();
                $deposit->txnid = $transaction_id;
                $deposit->status = 1;
                $deposit->save();


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

            return redirect()->route('user-dashboard')->with('success',__('Balance has been added to your account.'));
            }else{
                $deposit = Deposit::where('user_id','=',Session::get('item_number'))->orderBy('created_at','desc')->first();
                $deposit->delete();
            }
            return redirect()->back()->with('unsuccess',__('Payment Cancelled.'));
    }
    
}