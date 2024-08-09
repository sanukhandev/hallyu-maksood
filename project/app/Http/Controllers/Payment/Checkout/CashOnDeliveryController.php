<?php

namespace App\Http\Controllers\Payment\Checkout;

use App\{Helpers\AuthHelper, Models\Cart, Models\Order, Classes\GeniusMailer};
use App\Models\Country;
use App\Models\Reward;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use OrderHelper;
use App\Helpers\APIHelper;

class CashOnDeliveryController extends CheckoutBaseControlller
{

    private APIHelper $apiHelper;

    public function __construct()
    {
        $this->apiHelper = new APIHelper();
    }
    public function store(Request $request)
    {
        $input = $request->all();

        // Authentication check
        if ($request->pass_check) {
            $auth = OrderHelper::auth_check($input);
            if (!$auth['auth_success']) {
                return redirect()->back()->with('unsuccess', $auth['error_message']);
            }
        }

        // Check if the cart is empty
        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success', __("You don't have any product to checkout."));
        }

        // Retrieve the cart from the session
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        OrderHelper::license_check($cart); // License check

        // Rebuild the cart
        $newCart = [
            'totalQty' => $cart->totalQty,
            'totalPrice' => $cart->totalPrice,
            'items' => $cart->items
        ];
        $newCartJson = json_encode($newCart);

        // Affiliate user check
        $affilateUsers = OrderHelper::product_affilate_check($cart);
        $affilateUsersJson = $affilateUsers ? json_encode($affilateUsers) : null;

        // Prepare the order
        $order = new Order;
        $input['user_id'] = Auth::check() ? Auth::user()->id : null;
        $input['cart'] = $newCartJson;
        $input['affilate_users'] = $affilateUsersJson;
        $input['pay_amount'] = $request->total ;
        $input['order_number'] = APIHelper::generateOrderNumber();
        $input['wallet_price'] = $request->wallet_price;

        // Tax location
        $input['tax_location'] = $input['tax_type'] == 'state_tax'
            ? State::findOrFail($input['tax'])->state
            : Country::findOrFail($input['tax'])->country_name;
        $input['tax'] = Session::get('current_tax');

        // Affiliate charge calculation
        if (Session::has('affilate')) {
            $val = $request->total / $this->curr->value / 100 * $this->gs->affilate_charge;
            $sub = $affilateUsers ? array_reduce($affilateUsers, fn($carry, $item) => $carry + $item['charge'], $val) : $val;
            if ($sub > 0) {
                $user = OrderHelper::affilate_check(Session::get('affilate'), $sub, $input['dp']);
                $input['affilate_user'] = Session::get('affilate');
                $input['affilate_charge'] = $sub;
            }
        }

        // Save the order
        $order->fill($input)->save();
        $order->tracks()->create(['title' => 'Pending', 'text' => 'You have successfully placed your order.']);
        $order->notifications()->create();

        // Coupon check
        if ($input['coupon_id']) {
            OrderHelper::coupon_check($input['coupon_id']);
        }

        // Reward points calculation
        if (Auth::check() && $this->gs->is_reward) {
            $reward = Reward::where('order_amount', Reward::all()->sortBy(fn($reward) => abs($reward->order_amount - $order->pay_amount))->first()->order_amount)->first();
            Auth::user()->update(['reward' => Auth::user()->reward + $reward->reward]);
        }

        // Various checks
        OrderHelper::size_qty_check($cart);
        OrderHelper::stock_check($cart);
        OrderHelper::vendor_order_check($cart, $order);

        // Store the order and cart in session, then clear the cart
        Session::put('temporder', $order);
        Session::put('tempcart', $cart);
        Session::forget(['cart', 'already', 'coupon', 'coupon_total', 'coupon_total1', 'coupon_percentage']);

        // Add to transaction if applicable
        if ($order->user_id && $order->wallet_price) {
            OrderHelper::add_to_transaction($order, $order->wallet_price);
        }

        // Send emails
        $this->sendOrderEmails($order);

        // Redirect to success URL
        return redirect(route('front.payment.return'));
    }

    private function sendOrderEmails(Order $order)
    {
        // Sending Email to Buyer
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
        (new GeniusMailer())->sendAutoOrderMail($data, $order->id);

        // Sending Email to Admin
        $data = [
            'to' => 'support@hallyub2b.com',
            'subject' => "New Order Received!!",
            'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is " . $order->order_number . ".Please login to your panel to check. <br>Thank you.",
        ];
        (new GeniusMailer())->sendCustomMail($data);
    }

}
