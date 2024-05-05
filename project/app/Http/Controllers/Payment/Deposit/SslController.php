<?php

namespace App\Http\Controllers\Payment\Deposit;

use App\{
    Models\Deposit,
    Models\Transaction,
    Classes\GeniusMailer,
    Models\PaymentGateway
};

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class SslController extends DepositBaseController
{

    public function store(Request $request){

        $data = PaymentGateway::whereKeyword('sslcommerz')->first();
        $user = $this->user;

        $item_amount = $request->amount;
        $curr = $this->curr;

        $txnid = "SSLCZ_TXN_".uniqid();

        $supported_currency = json_decode($data->currency_id,true);
        if(!in_array($curr->id,$supported_currency)){
            return redirect()->back()->with('unsuccess',__('Invalid Currency For sslcommerz  Payment.'));
        }

        $cancel_url =  route('deposit.payment.cancle');
        $notify_url = route('deposit.ssl.notify');

        $deposit = new Deposit;
        $deposit->user_id = $user->id;
        $deposit->currency = $this->curr->sign;
        $deposit->currency_code = $this->curr->name;
        $deposit->amount = $request->amount / $this->curr->value;
        $deposit->currency_value = $this->curr->value;
        $deposit->method = 'SSLCommerz';
        $deposit->txnid = $txnid;
        $deposit->save();

        $paydata = $data->convertAutoData();

        $post_data = array();
        $post_data['store_id'] = $paydata['store_id'];
        $post_data['store_passwd'] = $paydata['store_password'];
        $post_data['total_amount'] = $item_amount;
        $post_data['currency'] = $curr->name;
        $post_data['tran_id'] = $txnid;
        $post_data['success_url'] = $notify_url;
        $post_data['fail_url'] =  $cancel_url;
        $post_data['cancel_url'] =  $cancel_url;
        # $post_data['multi_card_name'] = "mastercard,visacard,amexcard";  # DISABLE TO DISPLAY ALL AVAILABLE
        
        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $user->name;
        $post_data['cus_email'] = $user->email;
        $post_data['cus_add1'] = $user->address;
        $post_data['cus_city'] = $user->city;
        $post_data['cus_state'] = $user->state;
        $post_data['cus_postcode'] = $user->zip;
        $post_data['cus_country'] = $user->country;
        $post_data['cus_phone'] = $user->phone;
        $post_data['cus_fax'] = $user->phone;
        
        # REQUEST SEND TO SSLCOMMERZ
        if($paydata['sandbox_check'] == 1){
            $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";
        }
        else{
        $direct_api_url = "https://securepay.sslcommerz.com/gwprocess/v3/api.php";
        }


        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url );
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1 );
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC
        
        $content = curl_exec($handle );
        
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        
        if($code == 200 && !( curl_errno($handle))) {
            curl_close( $handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close( $handle);
            return redirect()->back()->with('unsuccess',__("FAILED TO CONNECT WITH SSLCOMMERZ API"));
            exit;
        }
        
        # PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true );
        
  
        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
        
             # THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
            # echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
            echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
            # header("Location: ". $sslcz['GatewayPageURL']);
            exit;
        } else {
            return redirect()->back()->with('unsuccess',__("JSON Data parsing error!"));
        }

 }

    
    public function notify(Request $request){

        $input = $request->all();

        $cancel_url = route('deposit.payment.cancle');
        $success_url = route('deposit.payment.return');

        if($input['status'] == 'VALID'){


            $deposit = Deposit::where('txnid','=',$input['tran_id'])->orderBy('created_at','desc')->first();
            $user = \App\Models\User::findOrFail($deposit->user_id);

            $user->balance = $user->balance + ($deposit->amount);
            $user->save();

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
            return redirect($cancel_url);
        }

    }
}