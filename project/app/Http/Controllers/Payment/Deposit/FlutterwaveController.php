<?php

namespace App\Http\Controllers\Payment\Deposit;

use App\{
    Models\Deposit,
    Models\Transaction,
    Classes\GeniusMailer,
    Models\PaymentGateway
};

use Session;
use OrderHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FlutterwaveController extends DepositBaseController
{

    public $public_key;
    private $secret_key;

    public function __construct()
    {
        parent::__construct();
        $data = PaymentGateway::whereKeyword('flutterwave')->first();
        $paydata = $data->convertAutoData();
        $this->public_key = $paydata['public_key'];
        $this->secret_key = $paydata['secret_key'];
    }

    public function store(Request $request) {

        $data = PaymentGateway::whereKeyword('flutterwave')->first();
        $user = $this->user;

        $item_amount = $request->amount;
        $curr = $this->curr;


        $item_number = Str::random(4).time();

        $supported_currency = json_decode($data->currency_id,true);
        if(!in_array($curr->id,$supported_currency)){
            return redirect()->back()->with('unsuccess',__('Invalid Currency For Flutterwave Payment.'));
        }

        $deposit = new Deposit;
        $deposit->user_id = $user->id;
        $deposit->currency = $this->curr->sign;
        $deposit->currency_code = $this->curr->name;
        $deposit->amount = $request->amount / $this->curr->value;
        $deposit->currency_value = $this->curr->value;
        $deposit->method = 'Flutterwave';
        $deposit->flutter_id = $item_number;
        $deposit->save();

        // SET CURL

        $curl = curl_init();

        $customer_email = $user->email;
        $amount = $item_amount;  
        $currency = $curr->name;
        $txref = $item_number; // ensure you generate unique references per transaction.
        $PBFPubKey = $this->public_key; // get your public key from the dashboard.
        $redirect_url = route('deposit.flutter.notify');
        $payment_plan = ""; // this is only required for recurring payments.

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
              'amount' => $amount,
              'customer_email' => $customer_email,
              'currency' => $currency,
              'txref' => $txref,
              'PBFPubKey' => $PBFPubKey,
              'redirect_url' => $redirect_url,
              'payment_plan' => $payment_plan
            ]),
            CURLOPT_HTTPHEADER => [
              "content-type: application/json",
              "cache-control: no-cache"
            ],
          ));
          
          $response = curl_exec($curl);
          $err = curl_error($curl);
          
          if($err){
            // there was an error contacting the rave API
            die('Curl returned error: ' . $err);
          }
          
          $transaction = json_decode($response);
          
          if(!$transaction->data && !$transaction->data->link){
            // there was an error from the API
            print_r('API returned error: ' . $transaction->message);
          }
         
          return redirect($transaction->data->link);

     }


     public function notify(Request $request) {

        $input = $request->all();

        $success_url = route('user.payment.return');
        $cancel_url = route('user.payment.cancle');

        if($request->cancelled == "true"){
          return redirect()->route('user-dashboard')->with('success',__('Payment Cancelled!'));
        }


        if (isset($input['txref'])) {

            $ref = $input['txref'];

            $query = array(
                "SECKEY" => $this->secret_key,
                "txref" => $ref
            );

            $data_string = json_encode($query);
                
            $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    
            $response = curl_exec($ch);
    
            curl_close($ch);
    
            $resp = json_decode($response, true);

            if ($resp['status'] == "success") {

              $paymentStatus = $resp['data']['status'];
              $chargeResponsecode = $resp['data']['chargecode'];

              if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($paymentStatus == "successful")) {

              
              $deposit = Deposit::where('flutter_id','=',$input['txref'])->orderBy('created_at','desc')->first();
              $user = \App\Models\User::findOrFail($deposit->user_id);

              $user->balance = $user->balance + ($deposit->amount);
              $user->save();
              $deposit->txnid =  $resp['data']['txid'];
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
              

              return redirect($success_url);
              
              }

              else {
                $deposit = Deposit::where('flutter_id','=',$input['txref'])
                ->orderBy('created_at','desc')->first();
                  $deposit->delete();
                  return redirect($cancel_url);
              }


            }
        }
            else {
              $deposit = Deposit::where('flutter_id','=',$input['txref'])
              ->orderBy('created_at','desc')->first();
                $deposit->delete();
                return redirect($cancel_url);
            }

     }
}