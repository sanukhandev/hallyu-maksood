<?php

namespace App\Http\Controllers\Payment\Deposit;

use App\{
    Traits\Paytm,
    Models\Deposit,
    Models\Transaction,
	Classes\GeniusMailer,
    Models\PaymentGateway
};

use Illuminate\{
	Http\Request,
	Support\Facades\Session
};
use Illuminate\Support\Str;


class PaytmController extends DepositBaseController
{

    use Paytm;
    public function store(Request $request)
    {

     $data = PaymentGateway::whereKeyword('paytm')->first();   
     $user = $this->user;
     
     $item_amount = $request->amount;
     $curr = $this->curr;

    $supported_currency = json_decode($data->currency_id,true);
    if(!in_array($curr->id,$supported_currency)){
        return redirect()->back()->with('unsuccess',__('Invalid Currency For Paytm Payment.'));
    }
     
     $return_url = route('deposit.payment.return');
     $cancel_url = route('deposit.payment.cancle');
     $notify_url = route('deposit.paytm.notify');
     $item_name = "Deposit via Paytm";
     $item_number = Str::random(4).time();

     $deposit = new Deposit;
     $deposit->user_id = $user->id;
     $deposit->currency = $this->curr->sign;
     $deposit->currency_code = $this->curr->name;
     $deposit->amount = $request->amount / $this->curr->value;
     $deposit->currency_value = $this->curr->value;
     $deposit->method = 'Paytm';
     $deposit->save();

        Session::put('item_number',$user->id); 

  
        $data_for_request = $this->handlePaytmRequest( $item_number, $item_amount, 'deposit');
        $paytm_txn_url = 'https://securegw-stage.paytm.in/theia/processTransaction';
        $paramList = $data_for_request['paramList'];
        $checkSum = $data_for_request['checkSum'];
        return view( 'frontend.paytm-merchant-form', compact( 'paytm_txn_url', 'paramList', 'checkSum' ) );
    }


	public function notify( Request $request ) {

		$order_id = $request['ORDERID'];

     

		if ( 'TXN_SUCCESS' === $request['STATUS'] ) {
		$transaction_id = $request['TXNID'];

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
