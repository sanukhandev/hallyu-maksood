<?php

namespace App\Http\Controllers\Payment\Deposit;

use App\{
    Models\Deposit,
    Models\Transaction,
    Classes\GeniusMailer,
    Models\PaymentGateway
};

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Twocheckout;
use Twocheckout_Charge;
use Twocheckout_Error;

class TwoCheckoutController extends DepositBaseController
{
    public function store(Request $request){

        $data = PaymentGateway::whereKeyword('2checkout')->first();

        $item_amount = $request->amount;
        $curr = $this->curr;

        $user = $this->user;
        $item_number = Str::random(4).time();

        $supported_currency = json_decode($data->currency_id,true);
        if(!in_array($curr->id,$supported_currency)){
            return redirect()->back()->with('unsuccess',__('Invalid Currency For Two Checkout  Payment.'));
        }


        $paydata = $data->convertAutoData();
        Twocheckout::privateKey($paydata['private_key']);
        Twocheckout::sellerId($paydata['seller_id']);
        if($paydata['sandbox_check'] == 1) {
            Twocheckout::sandbox(true);
        }
        else {
            Twocheckout::sandbox(false);
        }
    
            try {
    
                $charge = Twocheckout_Charge::auth(array(
                    "merchantOrderId" => $item_number,
                    "token"      => $request->token,
                    "currency"   => $curr->name,
                    "total"      => $item_amount,
                    "billingAddr" => array(
                        "name" => $user->name,
                        "addrLine1" => $user->address,
                        "city" => $user->city,
                        "state" => 'UN',
                        "zipCode" => $user->zip,
                        "country" => $user->country,
                        "email" => $user->email,
                        "phoneNumber" => $user->phone
                    )
                ));
            
                if ($charge['response']['responseCode'] == 'APPROVED') {
        
                    $user->balance = $user->balance + ($request->amount / $this->curr->value);
                    $user->mail_sent = 1;
                    $user->save();
  
                    $deposit = new Deposit;
                    $deposit->user_id = $user->id;
                    $deposit->currency = $this->curr->sign;
                    $deposit->currency_code = $this->curr->name;
                    $deposit->currency_value = $this->curr->value;
                    $deposit->amount = $request->amount / $this->curr->value;
                    $deposit->method = '2Checkout';
                    $deposit->txnid = $charge['response']['transactionId'];
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
  
  
                      $data = [
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
                      $mailer->sendAutoMail($data);

  
                    return redirect()->route('user-dashboard')->with('success',__('Balance has been added to your account.'));
            
                }
        
            } catch (Twocheckout_Error $e) {
                return redirect()->back()->with('unsuccess',$e->getMessage());
        
            }
        }
}