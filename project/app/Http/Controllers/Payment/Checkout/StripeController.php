<?php

namespace App\Http\Controllers\Payment\Checkout;

use App\{
    Models\Cart,
    Models\Order,
    Models\PaymentGateway,
    Classes\GeniusMailer
};
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Support\Facades\Auth;
use Session;
use OrderHelper;
use Illuminate\Support\Str;

class StripeController extends CheckoutBaseControlller
{
    public function __construct()
    {
        parent::__construct();
        $data = PaymentGateway::whereKeyword('stripe')->first();
        $paydata = $data->convertAutoData();
        \Config::set('services.stripe.key', $paydata['key']);
        \Config::set('services.stripe.secret', $paydata['secret']);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $data = PaymentGateway::whereKeyword('stripe')->first();
        $total = $request->total;

        if($request->pass_check) {
            $auth = OrderHelper::auth_check($input); // For Authentication Checking
            if(!$auth['auth_success']){
                return redirect()->back()->with('unsuccess',$auth['error_message']);
            }
        }

        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',__("You don't have any product to checkout."));
        }

        $item_name = $this->gs->title." Order";
        $item_number = Str::random(4).time();
        $item_amount = $total;
        $success_url = route('front.payment.return');

        // Validate Card Data

        $validator = \Validator::make($request->all(),[
            'cardNumber' => 'required',
            'cardCVC' => 'required',
            'month' => 'required',
            'year' => 'required',
        ]);


        if ($validator->passes()) {
            $stripe = Stripe::make(\Config::get('services.stripe.secret'));
            try{
                $token = $stripe->tokens()->create([
                    'card' =>[
                            'number' => $input['cardNumber'],
                            'exp_month' => $input['month'],
                            'exp_year' => $input['year'],
                            'cvc' => $input['cardCVC'],
                        ],
                    ]);
                if (!isset($token['id'])) {
                    return back()->with('error',__('Token Problem With Your Token.'));
                }

                $charge = $stripe->charges()->create([
                    'card' => $token['id'],
                    'currency' => $this->curr->name,
                    'amount' => $item_amount,
                    'description' => $item_name,
                    ]);

                if ($charge['status'] == 'succeeded') {

                    $oldCart = Session::get('cart');
                    $cart = new Cart($oldCart);
                    OrderHelper::license_check($cart); // For License Checking
                    $t_oldCart = Session::get('cart');
                    $t_cart = new Cart($t_oldCart);
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
                    $input['pay_amount'] = $item_amount / $this->curr->value;
                    $input['order_number'] = $item_number;
                    $input['wallet_price'] = $request->wallet_price / $this->curr->value;
                    $input['payment_status'] = "Completed";
                    $input['txnid'] = $charge['balance_transaction'];
                    $input['charge_id'] = $charge['id'];
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

            }catch (Exception $e){
                return back()->with('unsuccess', $e->getMessage());
            }catch (\Cartalyst\Stripe\Exception\CardErrorException $e){
                return back()->with('unsuccess', $e->getMessage());
            }catch (\Cartalyst\Stripe\Exception\MissingParameterException $e){
                return back()->with('unsuccess', $e->getMessage());
            }
        }
            return back()->with('unsuccess', __('Please Enter Valid Credit Card Informations.'));

    }
}
