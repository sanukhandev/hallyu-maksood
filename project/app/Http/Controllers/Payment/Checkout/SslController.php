<?php

namespace App\Http\Controllers\Payment\Checkout;

use App\{
    Models\Cart,
    Models\Order,
    Classes\GeniusMailer,
    Models\PaymentGateway
};
use App\Models\Country;
use App\Models\Reward;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use OrderHelper;
use Illuminate\Support\Str;

class SslController extends CheckoutBaseControlller
{
    public function store(Request $request)
    {
        $input = $request->all();

        $data = PaymentGateway::whereKeyword('sslcommerz')->first();

        $total = $request->total;

        $paydata = $data->convertAutoData();

        if($request->pass_check) {
            $auth = OrderHelper::auth_check($input); // For Authentication Checking
            if(!$auth['auth_success']){
                return redirect()->back()->with('unsuccess',$auth['error_message']);
            }
        }


        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',__("You don't have any product to checkout."));
        }

        $data['item_name'] = $this->gs->title." Order";
        $data['item_number'] = Str::random(4).time();
        $data['item_amount'] = $total;
        $data['txnid'] = "SSLCZ_TXN_".uniqid();
        $cancel_url = route('front.payment.cancle');
        $notify_url = route('front.ssl.notify');

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
        $input['pay_amount'] = $data['item_amount'] / $this->curr->value;
        $input['order_number'] = $data['item_number'];
        $input['wallet_price'] = $input['wallet_price'] / $this->curr->value;
        $input['payment_status'] = "Pending";
        $input['txnid'] = $data['txnid'];


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

        if($input['coupon_id'] != "") {
            OrderHelper::coupon_check($input['coupon_id']); // For Coupon Checking
        }

        $post_data = array();
        $post_data['store_id'] = $paydata['store_id'];
        $post_data['store_passwd'] = $paydata['store_password'];
        $post_data['total_amount'] = $data['item_amount'];
        $post_data['currency'] = $this->curr->name;
        $post_data['tran_id'] = $data['txnid'];
        $post_data['success_url'] = $notify_url;
        $post_data['fail_url'] =  $cancel_url;
        $post_data['cancel_url'] =  $cancel_url;
        # $post_data['multi_card_name'] = "mastercard,visacard,amexcard";  # DISABLE TO DISPLAY ALL AVAILABLE

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $input['customer_name'];
        $post_data['cus_email'] = $input['customer_email'];
        $post_data['cus_add1'] = $input['customer_address'];
        $post_data['cus_city'] = $input['customer_city'];
        $post_data['cus_state'] = $input['customer_state'];
        $post_data['cus_postcode'] = $input['customer_zip'];
        $post_data['cus_country'] = $input['customer_country'];
        $post_data['cus_phone'] = $input['customer_phone'];
        $post_data['cus_fax'] = $input['customer_phone'];

        # REQUEST SEND TO SSLCOMMERZ
        if($paydata['sandbox_check'] == 1){
            $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";
        }
        else{
        $direct_api_url = "https://securepay.sslcommerz.com/gwprocess/v3/api.php";
        }


        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url );
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1 );
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


        $content = curl_exec($handle );

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);


        if($code == 200 && !( curl_errno($handle))) {
            curl_close( $handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close( $handle);
            return redirect($cancel_url);
            exit;
        }

        # PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true );


        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
            echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
            # header("Location: ". $sslcz['GatewayPageURL']);
            exit;
        } else {
            return redirect($cancel_url);

        }
    }


    public function notify(Request $request)
    {
        $input_data = $request->all();

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

        $cart = Session::get('cart');

        if($input_data['status'] == 'VALID'){

                $order = Order::where('txnid',$request->tran_id)->first();
                $order->payment_status = 'Completed';
                $order->update();

                $order->tracks()->create(['title' => 'Pending', 'text' => 'You have successfully placed your order.' ]);
                $order->notifications()->create();



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
                Session::put('tempcart',$cart);
                Session::forget('cart');
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
        return redirect($cancel_url);
    }

}
