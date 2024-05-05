<?php

namespace App\Http\Controllers\Vendor;

use App\{
    Models\Order,
    Models\VendorOrder
};
use Illuminate\Http\Request;
use Datatables;

class OrderController extends VendorBaseController
{

    //*** JSON Request
    public function datatables()
    {
        $user = $this->user;
        $datas = Order::with(array('vendororders' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }))->get()->reject(function($item) use ($user){
            if($item->vendororders()->where('user_id','=',$user->id)->count() == 0){
                return true;
            }
            return false;
          });
   
         
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->editColumn('totalQty', function(Order $data) {
                                return $data->vendororders()->where('user_id','=',$this->user->id)->sum('qty');
                            })
                            ->editColumn('pay_amount', function (Order $data) {
                                return \PriceHelper::showOrderCurrencyPrice(($data->vendororders()->where('user_id','=',$this->user->id)->sum('price') * $data->currency_value),$data->currency_sign);
                            })
                            ->addColumn('action', function(Order $data) {
                                $pending = $data->vendororders()->where('user_id','=',$this->user->id)->where('status','pending')->count() > 0 ? "selected" : "";
                                $processing = $data->vendororders()->where('user_id','=',$this->user->id)->where('status','processing')->count() > 0 ? "selected" : "";
                                $completed = $data->vendororders()->where('user_id','=',$this->user->id)->where('status','completed')->count() > 0 ? "selected" : "";
                                $declined =  $data->vendororders()->where('user_id','=',$this->user->id)->where('status','declined')->count() > 0 ? "selected" : "";
                                return '
                                <div class="action-list">
                                <a href="'.route("vendor-order-show",$data->order_number).'" class="btn btn-primary product-btn"><i class="fa fa-eye"></i>  '.__("Details").' </a>
                                    <select class="vendor-btn  '.$data->vendororders()->where('user_id','=',$this->user->id)->first()->status.' ">
                                    <option value=" '.route("vendor-order-status",["id1" => $data->order_number, "status" => "pending"]).' "   '.$pending.'  > '.__("Pending").' </option>
                                    <option value=" '.route("vendor-order-status",["id1" => $data->order_number, "status" => "processing"]).' "  '.$processing.'   > '.__("Processing").' </option>
                                    <option value=" '.route("vendor-order-status",["id1" => $data->order_number, "status" => "completed"]).' "  '.$completed.'   > '.__("Completed").' </option>
                                    <option value=" '.route("vendor-order-status",["id1" => $data->order_number, "status" => "declined"]).' "  '.$declined.'   > '.__("Declined").' </option>
                                    </select>
                                </div>';
                            }) 
                            ->rawColumns(['id','action'])
                            ->toJson(); //--- Returning Json Data To Client Side

    }

    public function index(){
        return view('vendor.order.index');
    }


    public function show($slug)
    {
        $user = $this->user;
        $order = Order::where('order_number','=',$slug)->first();
        $cart = json_decode($order->cart, true);;
        return view('vendor.order.details',compact('user','order','cart'));
    }

    public function license(Request $request, $slug)
    {
        $order = Order::where('order_number','=',$slug)->first();
        $cart = json_decode($order->cart, true);
        $cart['items'][$request->license_key]['license'] = $request->license;
        $new_cart = json_encode($cart);
        $order->cart = $new_cart;
        $order->update();          
        $msg = __('Successfully Changed The License Key.');
        return redirect()->back()->with('license',$msg);
    }

    public function invoice($slug)
    {
        $user = $this->user;
        $order = Order::where('order_number','=',$slug)->first();
        $cart = json_decode($order->cart, true);;
        return view('vendor.order.invoice',compact('user','order','cart'));
    }

    public function printpage($slug)
    {
        $user = $this->user;
        $order = Order::where('order_number','=',$slug)->first();
        $cart = json_decode($order->cart, true);;
        return view('vendor.order.print',compact('user','order','cart'));
    }

    public function status($slug,$status)
    {
        $mainorder = VendorOrder::where('order_number','=',$slug)->first();
        if ($mainorder->status == "completed"){
            return redirect()->back()->with('success',__('This Order is Already Completed'));
        }else{
            $user = $this->user;
            VendorOrder::where('order_number','=',$slug)->where('user_id','=',$user->id)->update(['status' => $status]);
            return redirect()->route('vendor-order-index')->with('success',__('Order Status Updated Successfully'));
        }
    }

}
