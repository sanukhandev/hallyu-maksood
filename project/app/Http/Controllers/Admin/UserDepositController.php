<?php

namespace App\Http\Controllers\Admin;

use App\{
    Models\Deposit,
    Models\Transaction
};
use Illuminate\{
    Http\Request,
    Support\Str
};
use Validator;
use Datatables;

class UserDepositController extends AdminBaseController
{
    //*** JSON Request
    public function datatables($status)
    {
            $datas = Deposit::whereStatus($status)->Latest('id')->get();

             //--- Integrating This Collection Into Datatables
             return Datatables::of($datas)
                                ->addColumn('name', function(Deposit $data) {
                                    $name = '<a href="'.route('admin-user-show',$data->user_id).'" target="_blank">'.$data->user->name.'</a>';
                                    return $name;
                                })
                                ->editColumn('amount', function(Deposit $data) {
                                    $price = $data->amount * $data->currency_value;
                                    return \PriceHelper::showAdminCurrencyPrice($price);
                                })
                                ->addColumn('action', function(Deposit $data) {
                                    if($data->status == 1){
                                        return '<span class="badge badge-success deposit-completed">'.__("Completed").'</span';                                        
                                    }else{
                                        $class = $data->status == 1 ? 'drop-success' : 'drop-warning';
                                        $s = $data->status == 1 ? 'selected' : '';
                                        $ns = $data->status == 0 ? 'selected' : '';
                                        return '<div class="action-list"><select class="process select vendor-droplinks '.$class.'"><option data-val="1" value="'. route('admin-user-deposit-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>'.__("Completed").'</option><option data-val="0" value="'. route('admin-user-deposit-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>'.__("Pending").'</option></select></div>';
                                    }

                                }) 
                                ->rawColumns(['name','action'])
                                ->toJson(); //--- Returning Json Data To Client Side
    }

    //*** GET Request
    public function deposits($slug)
    {
        if($slug == 'all'){
            return view('admin.deposit.index');
        }else if($slug == 'pending'){
            return view('admin.deposit.pending');
        }

    }


	//*** GET Request
    public function status($id1,$id2)
    {
        $dep = Deposit::findOrFail($id1);
        $dep->status = $id2;
        $dep->update();

        $user = $dep->user;
        $user->balance = $user->balance + $dep->amount;
        $user->save();

        // store in transaction table
        if ($dep->status == 1) {
            $transaction = new Transaction;
            $transaction->txn_number = Str::random(3).substr(time(), 6,8).Str::random(3);
            $transaction->user_id = $dep->user_id;
            $transaction->amount = $dep->amount;
            $transaction->user_id = $dep->user_id;
            $transaction->currency_sign = $dep->currency;
            $transaction->currency_code = $dep->currency_code;
            $transaction->currency_value= $dep->currency_value;
            $transaction->method = $dep->method;
            $transaction->txnid = $dep->txnid;
            $transaction->details = 'Payment Deposit';
            $transaction->type = 'plus';
            $transaction->save();
        }
  
        //--- Redirect Section        
        $msg[0] = __('Status Updated Successfully.');
        return response()->json($msg);      
        //--- Redirect Section Ends    

    }

}