<?php

namespace App\Http\Controllers\Payment\Subscription;

use App\{
    Models\Subscription,
    Classes\GeniusMailer,
    Models\PaymentGateway,
    Models\UserSubscription,
    
};

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Twocheckout;
use Twocheckout_Charge;
use Twocheckout_Error;

class TwoCheckoutController extends SubscriptionBaseController
{
    public function store(Request $request){

        $this->validate($request, [
            'shop_name'   => 'unique:users',
           ],[ 
               'shop_name.unique' => __('This shop name has already been taken.')
            ]);

            $subs = Subscription::findOrFail($request->subs_id);
            $data = PaymentGateway::whereKeyword('2checkout')->first();
            $user = $this->user;

            $item_amount = $subs->price * $this->curr->value;
            $curr = $this->curr;

            $supported_currency = json_decode($data->currency_id,true);
            if(!in_array($curr->id,$supported_currency)){
                return redirect()->back()->with('unsuccess',__('Invalid Currency For Two Checkout  Payment.'));
            }

        $package = $user->subscribes()->where('status',1)->orderBy('id','desc')->first();

        $item_number = Str::random(4).time();
        $item_currency = $curr->name;

        $paydata = $data->convertAutoData();
        Twocheckout::privateKey($paydata['private_key']);
        Twocheckout::sellerId($paydata['seller_id']);
        if($paydata['sandbox_check'] == 1) {
            Twocheckout::sandbox(true);
        }
        else {
            Twocheckout::sandbox(false);
        }
    
            try {
    
                $charge = Twocheckout_Charge::auth(array(
                    "merchantOrderId" => $item_number,
                    "token"      => $request->token,
                    "currency"   => $item_currency,
                    "total"      => $item_amount,
                    "billingAddr" => array(
                        "name" => $user->name,
                        "addrLine1" => $user->address,
                        "city" => $user->city,
                        "state" => $user->state,
                        "zipCode" => $user->zip,
                        "country" => $user->country,
                        "email" => $user->email,
                        "phoneNumber" => $user->phone
                    )
                ));
            
                if ($charge['response']['responseCode'] == 'APPROVED') {
        
                    $today = Carbon::now()->format('Y-m-d');
                    $date = date('Y-m-d', strtotime($today.' + '.$subs->days.' days'));
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
                    $sub->method = '2Checkout';
                    $sub->txnid = $charge['response']['transactionId'];
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

                    return redirect()->route('user-dashboard')->with('success',__('Vendor Account Activated Successfully'));
            
                }
        
            } catch (Twocheckout_Error $e) {
                return redirect()->back()->with('unsuccess',$e->getMessage());
        
            }
    
    }
}
