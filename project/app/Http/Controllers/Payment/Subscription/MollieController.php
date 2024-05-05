<?php

namespace App\Http\Controllers\Payment\Subscription;

use App\{
    Models\User,
    Models\Subscription,
    Classes\GeniusMailer,
    Models\PaymentGateway,
    Models\UserSubscription
};

use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Str;

use Session;
use OrderHelper;
use Carbon\Carbon;

class MollieController extends SubscriptionBaseController
{

 public function store(Request $request){

    $this->validate($request, [
        'shop_name'   => 'unique:users',
    ],[ 
        'shop_name.unique' => __('This shop name has already been taken.')
    ]);

    $subs = Subscription::findOrFail($request->subs_id);
    $data = PaymentGateway::whereKeyword('mollie')->first();
    $user = $this->user;
    
    $item_amount = $subs->price * $this->curr->value;
    $curr = $this->curr;
    
    $supported_currency = json_decode($data->currency_id,true);
    if(!in_array($curr->id,$supported_currency)){
        return redirect()->back()->with('unsuccess',__('Invalid Currency For Molly Payment.'));
    }
    
     $input = $request->all();


     $notify_url = route('user.molly.notify');
     $order['item_name'] = $subs->title." Plan";
     $order['item_number'] = Str::random(4).time();
     $order['item_amount'] = $item_amount;

     $sub['user_id'] = $user->id;
     $sub['subscription_id'] = $subs->id;
     $sub['title'] = $subs->title;
     $sub['currency_sign'] = $this->curr->sign;
     $sub['currency_code'] = $this->curr->name;
     $sub['currency_value'] = $this->curr->value;
     $sub['price'] = $subs->price * $this->curr->value;
     $sub['price'] = $sub['price'] / $this->curr->value;
     $sub['days'] = $subs->days;
     $sub['allowed_products'] = $subs->allowed_products;
     $sub['details'] = $subs->details;
     $sub['method'] = 'Molly';     

        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => $curr->name,
                'value' => ''.sprintf('%0.2f', $order['item_amount']).'', // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'description' => $order['item_name'] ,
            'redirectUrl' => $notify_url,
            ]);

        Session::put('payment_id',$payment->id);
        Session::put('molly_data',$sub);
        Session::put('user_data',$input);
        Session::put('order_data',$order);

        $payment = Mollie::api()->payments()->get($payment->id);

        return redirect($payment->getCheckoutUrl(), 303);

 }


public function notify(Request $request){

        $sub = Session::get('molly_data');
        $input = Session::get('user_data');
        $order = Session::get('order_data');

        $success_url = route('user.payment.return');
        $cancel_url = route('user.payment.cancle');

        $payment = Mollie::api()->payments()->get(Session::get('payment_id'));

        if($payment->status == 'paid'){

            $order = new UserSubscription;
            $order->user_id = $sub['user_id'];
            $order->subscription_id = $sub['subscription_id'];
            $order->title = $sub['title'];
            $order->currency_sign = $this ->curr->sign;
            $order->currency_code = $this->curr->name;
            $order->currency_value = $this->curr->value;
            $order->price = $sub['price'];
            $order->days = $sub['days'];
            $order->allowed_products = $sub['allowed_products'];
            $order->details = $sub['details'];
            $order->method = $sub['method'];
            $order->txnid = $payment->id;
            $order->status = 1;

            $user = User::findOrFail($order->user_id);
            $package = $user->subscribes()->where('status',1)->orderBy('id','desc')->first();
            $subs = Subscription::findOrFail($order->subscription_id);

            $today = Carbon::now()->format('Y-m-d');
            $user->is_vendor = 2;
            if(!empty($package))
            {
                if($package->subscription_id == $order->subscription_id)
                {
                    $newday = strtotime($today);
                    $lastday = strtotime($user->date);
                    $secs = $lastday-$newday;
                    $days = $secs / 86400;
                    $total = $days+$subs->days;
                    $input['date'] = date('Y-m-d', strtotime($today.' + '.$total.' days'));
                }
                else
                {
                    $input['date'] = date('Y-m-d', strtotime($today.' + '.$subs->days.' days'));
                }
            }
            else
            {
                
                $input['date'] = date('Y-m-d', strtotime($today.' + '.$subs->days.' days'));

            }

            $input['mail_sent'] = 1;
            $user->update($input);
            $order->save();

            $maildata = [
                'to' => $user->email,
                'type' => "vendor_accept",
                'cname' => $user->name,
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
                'onumber' => "",
            ];

            $mailer = new GeniusMailer();
            $mailer->sendAutoMail($maildata);

            Session::forget('payment_id');
            Session::forget('molly_data');
            Session::forget('user_data');
            Session::forget('order_data');

            return redirect($success_url);
        }
        else {
            return redirect($cancel_url);
        }
}

}