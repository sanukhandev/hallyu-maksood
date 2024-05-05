<?php

namespace App\Http\Controllers\Payment\Subscription;

use App\{
    Models\User,
    Classes\Instamojo,
    Models\Subscription,
    Classes\GeniusMailer,
    Models\PaymentGateway,
    Models\UserSubscription
};

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class InstamojoController extends SubscriptionBaseController
{

 public function store(Request $request){


    $subs = Subscription::findOrFail($request->subs_id);

        $this->validate($request, [
            'shop_name'   => 'unique:users',
           ],[ 
               'shop_name.unique' => __('This shop name has already been taken.')
            ]);

            $subs = Subscription::findOrFail($request->subs_id);
            $data = PaymentGateway::whereKeyword('instamojo')->first();
            $user = $this->user;

            $item_amount = $subs->price * $this->curr->value;
            $curr = $this->curr;

            $supported_currency = json_decode($data->currency_id,true);
            if(!in_array($curr->id,$supported_currency)){
                return redirect()->back()->with('unsuccess',__('Invalid Currency For Instamojo Payment.'));
            }

        $input = $request->all();

        $cancel_url = route('user.payment.cancle');
        $notify_url = route('user.instamojo.notify');
        $item_name = $subs->title." Plan";

        Session::put('user_data',$input);

        $paydata = $data->convertAutoData();
        if($paydata['sandbox_check'] == 1){
        $api = new Instamojo($paydata['key'], $paydata['token'], 'https://test.instamojo.com/api/1.1/');
        }
        else {
        $api = new Instamojo($paydata['key'], $paydata['token']);
        }

        try {
        $response = $api->paymentRequestCreate(array(
            "purpose" => $item_name,
            "amount" => round($item_amount,2),
            "send_email" => false,
            "email" => $request->email,
            "redirect_url" => $notify_url
            ));
    
        $redirect_url = $response['longurl'];
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
        $sub['method'] = 'Instamojo';  
        $sub['pay_id'] = $response['id'];

        Session::put('subscription',$sub);

        $data['total'] =  $item_amount;
        $data['return_url'] = $notify_url;
        $data['cancel_url'] = $cancel_url;
        Session::put('paypal_items',$data);
        return redirect($redirect_url);
                
        }
        catch (Exception $e) {
            return redirect()->back()->with('unsuccess',$e->getMessage());
        }

 }


public function notify(Request $request){

        $data = $request->all();

        $sub = Session::get('subscription');

        $input = Session::get('user_data');

        $success_url = route('user.payment.return');
        $cancel_url  = route('user.payment.cancle');


        if($sub['pay_id'] == $data['payment_request_id']){

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
            $order->txnid = $data['payment_id'];
            $order->status = 1;

        $user = User::findOrFail($order->user_id);
        $package = $user->subscribes()->where('status',1)->orderBy('id','desc')->first();
        $subs = Subscription::findOrFail($order->subscription_id);

        $today = Carbon::now()->format('Y-m-d');

        $input['is_vendor'] = 2;

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
            $input['date']= date('Y-m-d', strtotime($today.' + '.$subs->days.' days'));
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


        Session::forget('subscription');

            return redirect($success_url);
        }
        else {
            return redirect($cancel_url);
        }

        return redirect()->route('user.payment.return');
}

}