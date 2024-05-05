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

class FlutterwaveController extends CheckoutBaseControlller
{
    public $public_key;
    private $secret_key;

    public function __construct()
    {
        parent::__construct();
        $data = PaymentGateway::whereKeyword('flutterwave')->first();
        $paydata = $data->convertAutoData();
        $this->public_key = $paydata['public_key'];
        $this->secret_key = $paydata['secret_key'];
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $data = PaymentGateway::whereKeyword('flutterwave')->first();
        $curr = $this->curr;
        $total = $request->total;

        $supported_currency = json_decode($data->currency_id,true);
        if(!in_array($curr->id,$supported_currency)){
            return redirect()->back()->with('unsuccess',__('Invalid Currency For Flutterwave Payment.'));
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
        $notify_url = route('front.flutter.notify');

        Session::put('input_data',$input);
        Session::put('order_data',$order);
        Session::put('order_payment_id', $order['item_number']);

        // SET CURL

        $curl = curl_init();

        $customer_email = $request->customer_email;
        $amount = $order['item_amount'];
        $currency = $this->curr->name;
        $txref = $order['item_number']; // ensure you generate unique references per transaction.
        $PBFPubKey = $this->public_key; // get your public key from the dashboard.
        $redirect_url = $notify_url;
        $payment_plan = ""; // this is only required for recurring payments.


        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode([
            'amount' => $amount,
            'customer_email' => $customer_email,
            'currency' => $currency,
            'txref' => $txref,
            'PBFPubKey' => $PBFPubKey,
            'redirect_url' => $redirect_url,
            'payment_plan' => $payment_plan
          ]),
          CURLOPT_HTTPHEADER => [
            "content-type: application/json",
            "cache-control: no-cache"
          ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($err){
          // there was an error contacting the rave API
          return redirect($cancel_url)->with('unsuccess','Curl returned error: ' . $err);
        }

        $transaction = json_decode($response);

        if(!$transaction->data && !$transaction->data->link){
          // there was an error from the API
          return redirect($cancel_url)->with('unsuccess','API returned error: ' . $transaction->message);
        }

        return redirect($transaction->data->link);

    }


    public function notify(Request $request)
    {

        $input_data = $request->all();



        $input = Session::get('input_data');


        if($request->cancelled == "true"){
            return redirect()->route('front.cart')->with('success',__('Payment Cancelled!'));
        }


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

        if (isset($input_data['txref'])) {

            $ref = $payment_id;

            $query = array(
                "SECKEY" => $this->secret_key,
                "txref" => $ref
            );

            $data_string = json_encode($query);

            $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

            $response = curl_exec($ch);

            curl_close($ch);

            $resp = json_decode($response, true);

            if ($resp['status'] = "success") {
                if(!empty($resp['data'])){

                    $paymentStatus = $resp['data']['status'];
                    $chargeResponsecode = $resp['data']['chargecode'];

                    if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($paymentStatus == "successful")) {

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
                    $input['txnid'] = $resp['data']['txid'];

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
        }
        return redirect($cancel_url);
        }
        return redirect($cancel_url);
    }
}
