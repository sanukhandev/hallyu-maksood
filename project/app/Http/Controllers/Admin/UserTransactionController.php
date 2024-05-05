<?php

namespace App\Http\Controllers\Admin;

use Datatables;
use App\Models\Transaction;

class UserTransactionController extends AdminBaseController
{
        //*** JSON Request
        public function transdatatables()
        {
             $datas = Transaction::orderBy('id','desc')->get();
             //--- Integrating This Collection Into Datatables
             return Datatables::of($datas)
                                ->addColumn('name', function(Transaction $data) {
                                    $name = '<a href="'.route('admin-user-show',$data->user_id).'" target="_blank">'.$data->user['name'].'</a>';
                                    return $name;
                                })
                                ->addColumn('date', function(Transaction $data) {
                                    $date = date('Y-m-d',strtotime($data->created_at));
                                    return $date;
                                })
                                ->editColumn('amount', function(Transaction $data) {
                                    $price = $data->amount * $data->currency_value;
                                    $price = \PriceHelper::showOrderCurrencyPrice($price,$data->currency_sign);
                                    if($data->type == 'plus'){
                                        $price ='+'.$price;
                                    } else {
                                        $price ='-'.$price;
                                    }
                                    return  $price;
                                })
                                ->addColumn('action', function(Transaction $data) {
                                    return '<div class="action-list">
                                                <a href="javascript:;" data-href="' . route('admin-trans-show',$data->id) . '" class="view" data-toggle="modal" data-target="#modal1"> 
                                                <i class="fas fa-eye"></i> '.__("Details").'
                                                </a>
                                            </div>';
                                }) 
                                ->rawColumns(['name','action'])
                                ->toJson(); //--- Returning Json Data To Client Side
        }

        public function index(){
            return view('admin.trans.index');
        }

        //*** GET Request
        public function transhow($id)
        {
            $data = Transaction::find($id);
            return view('admin.trans.show',compact('data'));
        }

}