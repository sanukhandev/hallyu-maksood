<?php

namespace App\Http\Controllers\User;

use App\{
  Models\Transaction,
  Models\PaymentGateway
};

class DepositController extends UserBaseController
{
    public function index() {
      return view('user.deposit.index');
    }

    public function transactions() {
      return view('user.transactions');
    }

    public function transhow($id) {
      $data = Transaction::find($id);
      return view('load.transaction-details',compact('data'));
    }

    public function create() {
      $data['curr'] = $this->curr;
      $data['gateway']  = PaymentGateway::whereDeposit(1)->where('currency_id', 'like', "%\"{$this->curr->id}\"%")->latest('id')->get();
      $paystackData = PaymentGateway::whereKeyword('paystack')->first();
      $data['paystack'] = $paystackData->convertAutoData();
      return view('user.deposit.create', $data);
    }


    public function paycancle(){
      return redirect()->back()->with('unsuccess',__('Payment Cancelled.'));
    }

    public function payreturn(){
      return redirect()->route('user-dashboard')->with('success',__('Balance has been added to your account.'));
   }

}
