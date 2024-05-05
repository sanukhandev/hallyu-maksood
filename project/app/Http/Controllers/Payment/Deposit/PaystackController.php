<?php

namespace App\Http\Controllers\Payment\Deposit;

use App\{
    Models\Deposit,
    Models\Transaction,
    Classes\GeniusMailer
};

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaystackController extends DepositBaseController
{

    public function store(Request $request) {

        $user = $this->user;
        $curr = $this->curr;
  
        $deposit = new Deposit;
        $deposit->user_id = $user->id;
        $deposit->currency = $curr->sign;
        $deposit->currency_code = $curr->name;
        $deposit->currency_value = $curr->value;
        $deposit->amount = $request->amount / $curr->value;
        $deposit->method = 'Paystack';
        $deposit->txnid = $request->ref_id;
        $deposit->status = 1;
        $deposit->save();
  
        $user->balance = $user->balance + ($request->amount / $curr->value);
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

        return redirect()->route('user-dashboard')->with('success',__('Balance has been added to your account successfully.'));
  

    }    

}