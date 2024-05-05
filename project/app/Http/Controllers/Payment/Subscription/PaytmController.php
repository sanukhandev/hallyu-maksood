<?php

namespace App\Http\Controllers\Payment\Subscription;

use App\{
	Models\User,
    Traits\Paytm,
	Models\Subscription,
    Classes\GeniusMailer,
    Models\PaymentGateway,
	Models\UserSubscription
};

use Illuminate\{
	Http\Request,
	Support\Facades\Session
};

use Carbon\Carbon;
use Illuminate\Support\Str;

class PaytmController extends SubscriptionBaseController
{

    use Paytm;
    public function store(Request $request)
    {
        $this->validate($request, [
            'shop_name'   => 'unique:users',
           ],[ 
               'shop_name.unique' => __('This shop name has already been taken.')
            ]);

            $subs = Subscription::findOrFail($request->subs_id);
            $data = PaymentGateway::whereKeyword('paytm')->first();
            $user = $this->user;

            $item_amount = $subs->price * $this->curr->value;
            $curr = $this->curr;           

            $supported_currency = json_decode($data->currency_id,true);
            if(!in_array($curr->id,$supported_currency)){
                return redirect()->back()->with('unsuccess',__('Invalid Currency For Paytm Payment.'));
            }

			$item_name = $subs->title." Plan";
			$item_number = Str::random(4).time();

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
            $sub->method = 'Paytm';
            $sub->save();

            Session::put('item_number',$sub->user_id); 

            $s_datas = Session::all();
            $session_datas = json_encode($s_datas);
            file_put_contents(storage_path().'/paytm/'.$item_number.'.json', $session_datas); 

	    $data_for_request = $this->handlePaytmRequest( $item_number, $item_amount, 'subscription' );
	    $paytm_txn_url = 'https://securegw-stage.paytm.in/theia/processTransaction';
	    $paramList = $data_for_request['paramList'];
	    $checkSum = $data_for_request['checkSum'];
	    return view( 'front.paytm-merchant-form', compact( 'paytm_txn_url', 'paramList', 'checkSum' ) );
    }


	public function notify( Request $request ) {


		$input = $request->all();
		$order_id = $request['ORDERID'];

        if(file_exists(storage_path().'/paytm/'.$order_id.'.json')){
            $data_results = file_get_contents(storage_path().'/paytm/'.$order_id.'.json');
            $lang = json_decode($data_results, true);
            foreach($lang as $key => $lan){
                Session::put(''.$key,$lan);
            }
            unlink(storage_path().'/paytm/'.$order_id.'.json');
        }

		if ( 'TXN_SUCCESS' === $request['STATUS'] ) {
			$transaction_id = $request['TXNID'];
        $order = UserSubscription::where('user_id','=',Session::get('item_number'))
            ->orderBy('created_at','desc')->first();

        $user = User::findOrFail($order->user_id);
        $package = $user->subscribes()->where('status',1)->orderBy('id','desc')->first();
        $subs = Subscription::findOrFail($order->subscription_id);

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

        $data['txnid'] = $transaction_id;
        $data['status'] = 1;
        $order->update($data);
            $maildata = [
                'to' => $user->email,
                'type' => "vendor_accept",
                'cname' => $user->name,
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
                'onumber' => '',
            ];
            $mailer = new GeniusMailer();
            $mailer->sendAutoMail($maildata);
        
            return redirect()->route('user-dashboard')->with('success',__('Vendor Account Activated Successfully'));

		} else if( 'TXN_FAILURE' === $request['STATUS'] ){
            //return view( 'payment-failed' );
        $order = UserSubscription::where('user_id','=',Session::get('item_number'))
            ->orderBy('created_at','desc')->first();
            $order->delete();
            return redirect(route('user.payment.cancle'));
		}
    }
}