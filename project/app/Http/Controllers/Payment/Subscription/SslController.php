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
use Illuminate\Support\Str;

use Session;
use Carbon\Carbon;

class SslController extends SubscriptionBaseController
{

    public function store(Request $request){

        $this->validate($request, [
            'shop_name'   => 'unique:users',
           ],[ 
               'shop_name.unique' => __('This shop name has already been taken.')
            ]);

            $subs = Subscription::findOrFail($request->subs_id);
            $data = PaymentGateway::whereKeyword('sslcommerz')->first();
            $user = $this->user;
        
            $item_amount = $subs->price * $this->curr->value;
            $curr = $this->curr;  


            $supported_currency = json_decode($data->currency_id,true);
            if(!in_array($curr->id,$supported_currency)){
                return redirect()->back()->with('unsuccess',__('Invalid Currency For sslcommerz  Payment.'));
            }

        $txnid = "SSLCZ_TXN_".uniqid();
        

     
        $order['item_name'] = $subs->title." Plan";
        $order['item_number'] = Str::random(4).time();
        $order['item_amount'] = $item_amount ;


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
        $sub->method = 'SSLCommerz';
        $sub->txnid = $txnid;
        $sub->status = 0;
        $sub->save();

        $paydata = $data->convertAutoData();

        $post_data['store_id'] = $paydata['store_id'];
        $post_data['store_passwd'] = $paydata['store_password'];
        $post_data['total_amount'] = $order['item_amount'];
        $post_data['currency'] = $subs->currency_code;
        $post_data['tran_id'] = $txnid;
        $post_data['success_url'] = route('user.ssl.notify');
        $post_data['fail_url'] =  route('user.payment.cancle');
        $post_data['cancel_url'] =  route('user.payment.cancle');
        # $post_data['multi_card_name'] = "mastercard,visacard,amexcard";  # DISABLE TO DISPLAY ALL AVAILABLE
        
        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $user->name;
        $post_data['cus_email'] = $user->email;
        $post_data['cus_add1'] = $user->address;
        $post_data['cus_city'] = $user->city;
        $post_data['cus_state'] = $user->state;
        $post_data['cus_postcode'] = $user->zip;
        $post_data['cus_country'] = $user->country;
        $post_data['cus_phone'] = $user->phone;
        $post_data['cus_fax'] = $user->phone;
        
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
            return redirect()->back()->with('unsuccess',__("FAILED TO CONNECT WITH SSLCOMMERZ API"));
            exit;
        }
        
        # PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true );
        
        $s_datas = Session::all();
        $session_datas = json_encode($s_datas);
        file_put_contents(storage_path().'/ssl/'.$txnid.'.json', $session_datas); 

        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
        
             # THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
            # echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
            echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
            # header("Location: ". $sslcz['GatewayPageURL']);
            exit;
        } else {
            return redirect()->back()->with('unsuccess',__("JSON Data parsing error!"));
        }

 }

    public function notify(Request $request){


        $input = $request->all();

        if(file_exists(storage_path().'/ssl/'.$input['tran_id'].'.json')){
            $data_results = file_get_contents(storage_path().'/ssl/'.$input['tran_id'].'.json');
            $lang = json_decode($data_results, true);
            foreach($lang as $key => $lan){
                Session::put(''.$key,$lan);
            }
            unlink(storage_path().'/ssl/'.$input['tran_id'].'.json');
        }

        $success_url = route('user.payment.return');
        $cancel_url = route('user.payment.cancle');


        if($input['status'] == 'VALID'){

            $subs = UserSubscription::where('txnid','=',$input['tran_id'])->orderBy('id','desc')->first();
            $subs->status = 1;
            $subs->update();
            
            $user = User::findOrFail($subs->user_id);
            $package = $user->subscribes()->where('status',1)->orderBy('id','desc')->first();

            $today = Carbon::now()->format('Y-m-d');
            $input = $request->all();  
            $user->is_vendor = 2;
            if(!empty($package))
                    {

                        if($package->subscription_id == $subs->subscription_id)
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
           
            $user->update();

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
        else {
            return redirect($cancel_url);
        }

    }
}