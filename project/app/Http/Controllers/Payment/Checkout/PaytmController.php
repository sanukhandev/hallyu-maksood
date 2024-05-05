<?php

namespace App\Http\Controllers\Payment\Checkout;

use App\{
    Models\Cart,
    Models\Order,
    Traits\Paytm,
    Classes\GeniusMailer,
    Models\PaymentGateway
};
use App\Models\Country;
use App\Models\Reward;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Session;
use OrderHelper;

class PaytmController extends CheckoutBaseControlller
{
    use Paytm;

    public function store(Request $request)
    {
        $input = $request->all();
        $data = PaymentGateway::whereKeyword('paytm')->first();
        $total = $request->total;

        if($this->curr->name != "INR")
        {
            return redirect()->back()->with('unsuccess',__('Please Select INR Currency For This Payment.'));
        }

        if($request->pass_check) {
            $auth = OrderHelper::auth_check($input); // For Authentication Checking
            if(!$auth['auth_success']){
                return redirect()->back()->with('unsuccess',$auth['error_message']);
            }
        }

        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',__("You don't have any product to checkout."));
        }

        $order['item_name'] = $this->gs->title." Order";
        $order['item_number'] = Str::random(4).time();
        $order['item_amount'] = $total;
        $cancel_url = route('front.payment.cancle');

        Session::put('input_data',$input);
        Session::put('order_data',$order);
        Session::put('order_payment_id', $order['item_number']);

	    $data_for_request = $this->handlePaytmRequest( $order['item_number'], $order['item_amount'], 'checkout' );
	    $paytm_txn_url = 'https://securegw-stage.paytm.in/theia/processTransaction';
	    $paramList = $data_for_request['paramList'];
        $checkSum = $data_for_request['checkSum'];
        view()->share('langg', $this->language);
	    return view( 'frontend.paytm-merchant-form', compact( 'paytm_txn_url', 'paramList', 'checkSum' ) );
    }

    public function notify(Request $request)
    {
        $input_data = $request->all();


        $input = Session::get('input_data');
        $order_data = Session::get('order_data');
        $success_url = route('front.payment.return');
        $cancel_url = route('front.payment.cancle');

        /** Get the payment ID before session clear **/
        $payment_id = Session::get('order_payment_id');

        if (Session::has('currency')) {
            $this->curr = \DB::table('currencies')->find(Session::get('currency'));
        }
        else {
            $this->curr = \DB::table('currencies')->where('is_default','=',1)->first();
        }


        if ($input_data['STATUS'] == 'TXN_SUCCESS') {

            if($payment_id == $input_data['ORDERID']){

                $cart = Session::get('cart');
                OrderHelper::license_check($cart); // For License Checking

                $t_cart = new Cart($cart);
                $new_cart = [];
                $new_cart['totalQty'] = $t_cart->totalQty;
                $new_cart['totalPrice'] = $t_cart->totalPrice;
                $new_cart['items'] = $t_cart->items;
                $new_cart = json_encode($new_cart);

                $temp_affilate_users = OrderHelper::product_affilate_check($cart); // For Product Based Affilate Checking
                $affilate_users = $temp_affilate_users == null ? null : json_encode($temp_affilate_users);

                $order = new Order;
                $input['cart'] = $new_cart;
                $input['user_id'] = Auth::check() ? Auth::user()->id : NULL;
                $input['affilate_users'] = $affilate_users;
                $input['pay_amount'] = $order_data['item_amount'] / $this->curr->value;
                $input['order_number'] = $order_data['item_number'];
                $input['wallet_price'] = $input['wallet_price'] / $this->curr->value;
                $input['payment_status'] = "Completed";
                $input['txnid'] = $input_data['TXNID'];

                if($input['tax_type'] == 'state_tax'){
                    $input['tax_location'] = State::findOrFail($input['tax'])->state;
                }else{
                    $input['tax_location'] = Country::findOrFail($input['tax'])->country_name;
                }
                $input['tax'] = Session::get('current_tax');


                if($input['dp'] == 1){
                    $input['status'] = 'completed';
                }
                if (Session::has('affilate')) {
                    $val = $request->total / $this->curr->value;
                    $val = $val / 100;
                    $sub = $val * $this->gs->affilate_charge;
                    if($temp_affilate_users != null){
                        $t_sub = 0;
                        foreach($temp_affilate_users as $t_cost){
                            $t_sub += $t_cost['charge'];
                        }
                        $sub = $sub - $t_sub;
                    }
                    if($sub > 0){
                        $user = OrderHelper::affilate_check(Session::get('affilate'),$sub,$input['dp']); // For Affiliate Checking
                        $input['affilate_user'] = Session::get('affilate');
                        $input['affilate_charge'] = $sub;
                    }

                }

                $order->fill($input)->save();
                $order->tracks()->create(['title' => 'Pending', 'text' => 'You have successfully placed your order.' ]);
                $order->notifications()->create();

                if($input['coupon_id'] != "") {
                    OrderHelper::coupon_check($input['coupon_id']); // For Coupon Checking
                }

                if(Auth::check()){
                    if($this->gs->is_reward == 1){
                        $num = $order->pay_amount;
                        $rewards = Reward::get();
                        foreach ($rewards as $i) {
                            $smallest[$i->order_amount] = abs($i->order_amount - $num);
                        }

                        asort($smallest);
                        $final_reword = Reward::where('order_amount',key($smallest))->first();
                        Auth::user()->update(['reward' => (Auth::user()->reward + $final_reword->reward)]);
                    }
                }


                OrderHelper::size_qty_check($cart); // For Size Quantiy Checking
                OrderHelper::stock_check($cart); // For Stock Checking
                OrderHelper::vendor_order_check($cart,$order); // For Vendor Order Checking

                Session::put('temporder',$order);
                Session::put('tempcart',Session::get('cart'));
                Session::forget('cart');
                Session::forget('tcart');
                Session::forget('already');
                Session::forget('coupon');
                Session::forget('coupon_total');
                Session::forget('coupon_total1');
                Session::forget('coupon_percentage');

                if ($order->user_id != 0 && $order->wallet_price != 0) {
                    OrderHelper::add_to_transaction($order,$order->wallet_price); // Store To Transactions
                }

                //Sending Email To Buyer
                $data = [
                    'to' => $order->customer_email,
                    'type' => "new_order",
                    'cname' => $order->customer_name,
                    'oamount' => "",
                    'aname' => "",
                    'aemail' => "",
                    'wtitle' => "",
                    'onumber' => $order->order_number,
                ];
                $mailer = new GeniusMailer();
                $mailer->sendAutoOrderMail($data,$order->id);

                //Sending Email To Admin
                $data = [
                    'to' => $this->ps->contact_email,
                    'subject' => "New Order Recieved!!",
                    'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order->order_number.".Please login to your panel to check. <br>Thank you.",
                ];
                $mailer = new GeniusMailer();
                $mailer->sendCustomMail($data);

                return redirect($success_url);
            }

        }
        return redirect($cancel_url);
    }
}
