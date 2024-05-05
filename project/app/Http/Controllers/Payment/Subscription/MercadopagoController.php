<?php

namespace App\Http\Controllers\Payment\Subscription;

use App\{
    Models\Subscription,
    Classes\GeniusMailer,
    Models\PaymentGateway,
    Models\UserSubscription
};

use Illuminate\Http\Request;
use Carbon\Carbon;
use MercadoPago;

class MercadopagoController extends SubscriptionBaseController
{

    public function store(Request $request)
    {
   
        $this->validate($request, [
            'shop_name'   => 'unique:users',
           ],[ 
               'shop_name.unique' => __('This shop name has already been taken.')
            ]);
        $subs = Subscription::findOrFail($request->subs_id);
        $data = PaymentGateway::whereKeyword('mercadopago')->first();
        $user = $this->user;

        $item_amount = $subs->price * $this->curr->value;
        $curr = $this->curr;

        $supported_currency = json_decode($data->currency_id,true);
        if(!in_array($curr->id,$supported_currency)){
            return redirect()->back()->with('unsuccess',__('Invalid Currency For Mercadopago Payment.'));
        }

        $input = $request->all();
        
        $paydata = $data->convertAutoData();

        $package = $user->subscribes()->where('status',1)->orderBy('id','desc')->first();
        $success_url = route('user.payment.return');
        $item_name = $subs->title." Plan";

        MercadoPago\SDK::setAccessToken($paydata['token']);
        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = (string)$item_amount;
        $payment->token = $input['token'];
        $payment->description = $item_name;
        $payment->installments = 1;
        $payment->payer = array(
          "email" => $user->email
        );
        $payment->save();

        if ($payment->status == 'approved') {

            $today = Carbon::now()->format('Y-m-d');
            $input = $request->all();  
            $user->is_vendor = 2;
            if(!empty($package))
            {
                if($package->subscription_id == $request->subs_id)
                {
                    $newday = strtotime($today);
                    $lastday = strtotime($user->date);
                    $secs = $lastday-$newday;
                    $days = $secs / 86400;
                    $total = $days+$subs->days;
                    $user->date = date('Y-m-d', strtotime($today.' + '.$total.' days'));
                }
                else
                {
                    $user->date = date('Y-m-d', strtotime($today.' + '.$subs->days.' days'));
                }
            }
            else
            {
                $user->date = date('Y-m-d', strtotime($today.' + '.$subs->days.' days'));
            }
            $user->mail_sent = 1;     
            $user->update($input);
            $sub = new UserSubscription;
            $sub->user_id = $user->id;
            $sub->subscription_id = $subs->id;
            $sub->title = $subs->title;
            $sub->currency_sign = $this->curr->sign;
            $sub->currency_code = $this->curr->name;
            $sub->currency_value = $this->curr->value;
            $sub->price = $subs->price * $this->curr->value;
            $sub->price = $sub->price / $this->curr->value;
            $sub->days = $subs->days;
            $sub->allowed_products = $subs->allowed_products;
            $sub->details = $subs->details;
            $sub->method = 'Mercadopago';
            $sub->txnid = $payment->id;

            $sub->status = 1;
            $sub->save();

            $data = [
                'to' => $user->email,
                'type' => "vendor_accept",
                'cname' => $user->name,
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
                'onumber' => "",
            ];
            $mailer = new GeniusMailer();
            $mailer->sendAutoMail($data);        

            return redirect($success_url);
            
        } 

        return back()->with('unsuccess', __('Payment Failed.'));

    }
}