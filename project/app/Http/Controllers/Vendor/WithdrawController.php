<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Withdraw;
use Illuminate\Http\Request;

class WithdrawController extends VendorBaseController
{

  	public function index()
    {
        $withdraws = Withdraw::where('user_id','=',$this->user->id)->where('type','=','vendor')->latest('id')->get();
        $sign = $this->curr;        
        return view('vendor.withdraw.index',compact('withdraws','sign'));
    }


    public function create()
    {
        $sign = $this->curr;
        return view('vendor.withdraw.create' ,compact('sign'));
    }


    public function store(Request $request)
    {

        $from = $this->user;

        $withdrawcharge = $this->gs;
        $charge = $withdrawcharge->withdraw_fee;

        if($request->amount > 0){

            $amount = $request->amount;

            if ($from->current_balance >= $amount){
                $fee = (($withdrawcharge->withdraw_charge / 100) * $amount) + $charge;
                $finalamount = $amount - $fee;
                $finalamount = number_format((float)$finalamount,2,'.','');

                $from->current_balance = $from->current_balance - $amount;
                $from->update();

                $newwithdraw = new Withdraw();
                $newwithdraw['user_id'] = $this->user->id;
                $newwithdraw['method'] = $request->methods;
                $newwithdraw['acc_email'] = $request->acc_email;
                $newwithdraw['iban'] = $request->iban;
                $newwithdraw['country'] = $request->acc_country;
                $newwithdraw['acc_name'] = $request->acc_name;
                $newwithdraw['address'] = $request->address;
                $newwithdraw['swift'] = $request->swift;
                $newwithdraw['reference'] = $request->reference;
                $newwithdraw['amount'] = $finalamount;
                $newwithdraw['fee'] = $fee;
                $newwithdraw['type'] = 'vendor';
                $newwithdraw->save();

                return response()->json(__('Withdraw Request Sent Successfully.')); 

            }else{
                 return response()->json(array('errors' => [ 0 => __('Insufficient Balance.') ])); 
            }
        }
            return response()->json(array('errors' => [ 0 => __('Please enter a valid amount.') ])); 

    }
}
