<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        if($request->start_date && $request->end_date){
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            $datas = VendorOrder::with('order')->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date);
        }else{
            $datas = VendorOrder::with('order')->where('user_id',Auth::user()->id);
        }
       
        return view('vendor.earning',[
            'datas' => $datas->count() > 0 ? $datas->get() : [],
            'total' => $datas->count() > 0 ? $datas->get()[0]->order->currency_sign . $datas->sum('price') : 0,
            'start_date' => isset($start_date) ? $start_date : '',
            'end_date' => isset($end_date) ? $end_date : '',
        ]);
    }


}
