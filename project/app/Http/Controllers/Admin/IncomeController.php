<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Order;
use App\Models\UserSubscription;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IncomeController extends Controller
{


    public function taxCalculate(Request $request)
    {

        $current_date = Carbon::now();
        $explode = explode('-',$current_date->format('d-m-Y'));
        $explode[0] = '1';
        $implode= implode("-",$explode);
        $first_day = Carbon::parse($implode);
        $last30days = date('Y-m-d', strtotime('today - 30 days'));
        
        
        $last_30_days =  $orders = Order::whereDate('created_at','>=',$last30days)->whereDate('created_at','<=',$current_date)->select('id','order_number','txnId','created_at','tax','tax_location','currency_sign','currency_value')->where('tax','!=',0);
        $current_month =  $orders = Order::whereDate('created_at','>=',$first_day)->whereDate('created_at','<=',$current_date)->select('id','order_number','txnId','created_at','tax','tax_location','currency_sign','currency_value')->where('tax','!=',0);
        

         $sign = Currency::where('is_default','=',1)->first();
         if($request->start_date && $request->end_date){
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            $orders = Order::whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->select('id','order_number','txnId','created_at','tax','tax_location','currency_sign','currency_value')->where('tax','!=',0);
        }else{
            $orders = Order::select('id','order_number','txnId','created_at','tax','tax_location','currency_sign','currency_value')->where('tax','!=',0);
        }
       
        return view('admin.earning.tax_calculate',[
            'orders' => $orders->count() > 0 ? $orders->get() : [],
            'total' => $orders->count() > 0 ? $sign->sign . $orders->sum('tax') : 0,
            'start_date' => isset($start_date) ? $start_date : '',
            'end_date' => isset($end_date) ? $end_date : '',
            'currency' => $sign,
            'current_month' => $current_month->count() > 0 ? $sign->sign . $current_month->sum('tax') : 0,
           'last_30_days' => $last_30_days->count() > 0 ? $sign->sign . $last_30_days->sum('tax') : 0,
        ]);


    }


    public function subscriptionIncome(Request $request)
    {

        $current_date = Carbon::now();
        $explode = explode('-',$current_date->format('d-m-Y'));
        $explode[0] = '1';
        $implode= implode("-",$explode);
        $first_day = Carbon::parse($implode);
        $last30days = date('Y-m-d', strtotime('today - 30 days'));
        
        
        $last_30_days =  $orders = UserSubscription::whereDate('created_at','>=',$last30days)->whereDate('created_at','<=',$current_date)->select('id','order_number','txnId','created_at','tax','tax_location','currency_sign','currency_value')->select('id','txnId','created_at','price','method','title')->where('price',"!=",0);
        $current_month =  $orders = UserSubscription::whereDate('created_at','>=',$first_day)->whereDate('created_at','<=',$current_date)->select('id','order_number','txnId','created_at','tax','tax_location','currency_sign','currency_value')->select('id','txnId','created_at','price','method','title')->where('price',"!=",0);
        
        
        $sign = Currency::where('is_default','=',1)->first();
        if($request->start_date && $request->end_date){
           $start_date = Carbon::parse($request->start_date);
           $end_date = Carbon::parse($request->end_date);
           $orders = UserSubscription::whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->select('id','order_number','txnId','created_at','tax','tax_location','currency_sign','currency_value')->select('id','txnId','created_at','price','method','title')->where('price',"!=",0);
       }else{
           $orders = UserSubscription::select('id','order_number','txnId','created_at','tax','tax_location','currency_sign','currency_value')->select('id','txnId','created_at','price','method','title')->where('price',"!=",0);
       }
      
       return view('admin.earning.subscription_income',[
           'orders' => $orders->count() > 0 ? $orders->get() : [],
           'total' => $orders->count() > 0 ? $sign->sign . $orders->sum('price') : 0,
           'start_date' => isset($start_date) ? $start_date : '',
           'end_date' => isset($end_date) ? $end_date : '',
           'currency' => $sign,
           'current_month' => $current_month->count() > 0 ? $sign->sign . $current_month->sum('price') : 0,
           'last_30_days' => $last_30_days->count() > 0 ? $sign->sign . $last_30_days->sum('price') : 0,
       ]);


    }


    public function withdrawIncome(Request $request)
    {
   
        $current_date = Carbon::now();
        $explode = explode('-',$current_date->format('d-m-Y'));
        $explode[0] = '1';
        $implode= implode("-",$explode);
        $first_day = Carbon::parse($implode);
        $last30days = date('Y-m-d', strtotime('today - 30 days'));
        
        
        $last_30_days =  $orders = Withdraw::whereDate('created_at','>=',$last30days)->whereDate('created_at','<=',$current_date)->where('status','completed');
        $current_month =  $orders = Withdraw::whereDate('created_at','>=',$first_day)->whereDate('created_at','<=',$current_date)->where('status','completed');
        
        $sign = Currency::where('is_default','=',1)->first();
        if($request->start_date && $request->end_date){
           $start_date = Carbon::parse($request->start_date);
           $end_date = Carbon::parse($request->end_date);
           $orders = Withdraw::whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->where('status','completed');
       }else{
           $orders = Withdraw::where('status','completed');
       }
      
       return view('admin.earning.withdraw_income',[
           'orders' => $orders->count() > 0 ? $orders->get() : [],
           'total' => $orders->count() > 0 ? $sign->sign . $orders->sum('fee') : 0,
           'start_date' => isset($start_date) ? $start_date : '',
           'end_date' => isset($end_date) ? $end_date : '',
           'currency' => $sign,
           'current_month' => $current_month->count() > 0 ? $sign->sign . $current_month->sum('fee') : 0,
           'last_30_days' => $last_30_days->count() > 0 ? $sign->sign . $last_30_days->sum('fee') : 0,
       ]);

    }


    public function commissionIncome(Request $request)
    {

        $current_date = Carbon::now();
        $explode = explode('-',$current_date->format('d-m-Y'));
        $explode[0] = '1';
        $implode= implode("-",$explode);
        $first_day = Carbon::parse($implode);
        $last30days = date('Y-m-d', strtotime('today - 30 days'));
        
        
        $last_30_days =  Order::whereDate('created_at','>=',$last30days)->whereDate('created_at','<=',$current_date)->select('id','order_number','txnId','created_at','tax','tax_location','currency_sign','currency_value')->select('id','txnId','created_at','price','method','title')->where('commission','!=',0);
        $current_month =  Order::whereDate('created_at','>=',$first_day)->whereDate('created_at','<=',$current_date)->select('id','order_number','txnId','created_at','tax','tax_location','currency_sign','currency_value')->select('id','txnId','created_at','price','method','title')->where('commission','!=',0);
        
    
        $sign = Currency::where('is_default','=',1)->first();
        if($request->start_date && $request->end_date){
           $start_date = Carbon::parse($request->start_date);
           $end_date = Carbon::parse($request->end_date);
           $orders = Order::whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->where('commission','!=',0);
       }else{
           $orders = Order::where('commission','!=',0);
       }
      
       return view('admin.earning.commission_earning',[
           'orders' => $orders->count() > 0 ? $orders->get() : [],
           'total' => $orders->count() > 0 ? $sign->sign . $orders->sum('tax') : 0,
           'start_date' => isset($start_date) ? $start_date : '',
           'end_date' => isset($end_date) ? $end_date : '',
           'currency' => $sign,
           'current_month' => $current_month->count() > 0 ? $sign->sign . $current_month->sum('tax') : 0,
           'last_30_days' => $last_30_days->count() > 0 ? $sign->sign . $last_30_days->sum('tax') : 0,
       ]);


    }






}
