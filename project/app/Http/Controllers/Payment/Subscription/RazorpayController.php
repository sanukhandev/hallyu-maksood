<?php

namespace App\Http\Controllers\Payment\Subscription;

use App\{
    Models\User,
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
use Razorpay\Api\Api;
use Illuminate\Support\Str;


class RazorpayController extends SubscriptionBaseController
{

    public function __construct()
    {
        parent::__construct();
        $data = PaymentGateway::whereKeyword('razorpay')->first();
        $paydata = $data->convertAutoData();
        $this->keyId = $paydata['key'];
        $this->keySecret = $paydata['secret'];
        $this->displayCurrency = 'INR';
        $this->api = new Api($this->keyId, $this->keySecret);
    }

 public function store(Request $request){

    $this->validate($request, [
        'shop_name'   => 'unique:users',
       ],[ 
           'shop_name.unique' => __('This shop name has already been taken.')
        ]);

    $subs = Subscription::findOrFail($request->subs_id);
    $data = PaymentGateway::whereKeyword('razorpay')->first();
    $user = $this->user;

    $item_amount = $subs->price * $this->curr->value;
    $curr = $this->curr;           

    $supported_currency = json_decode($data->currency_id,true);
    if(!in_array($curr->id,$supported_currency)){
        return redirect()->back()->with('unsuccess',__('Invalid Currency For Razorpay Payment.'));
    }

     $this->displayCurrency = ''.$curr->name.'';

     $item_name = $subs->title." Plan";
     $item_number = Str::random(4).time();
     
     $cancel_url = route('user.payment.cancle');
     $notify_url = route('user.razorpay.notify');

        $orderData = [
            'receipt'         => $item_number,
            'amount'          => $item_amount * 100, // 2000 rupees in paise
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];
        
        $razorpayOrder = $this->api->order->create($orderData);
        
        $razorpayOrderId = $razorpayOrder['id'];
        
        session(['razorpay_order_id'=> $razorpayOrderId]);

    // Redirect to paypal IPN

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
                    $sub->method = 'Razorpay';
                    $sub->save();

                    $displayAmount = $amount = $orderData['amount'];
                    
                    if ($this->displayCurrency !== 'INR')
                    {
                        $url = "https://api.fixer.io/latest?symbols=$this->displayCurrency&base=INR";
                        $exchange = json_decode(file_get_contents($url), true);
                    
                        $displayAmount = $exchange['rates'][$this->displayCurrency] * $amount / 100;
                    }
                    
                    $checkout = 'automatic';
                    
                    if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
                    {
                        $checkout = $_GET['checkout'];
                    }
                    
                    $data = [
                        "key"               => $this->keyId,
                        "amount"            => $amount,
                        "name"              => $item_name,
                        "description"       => $item_name,
                        "prefill"           => [
                            "name"              => $user->name,
                            "email"             => $user->email,
                            "contact"           => $user->phone,
                        ],
                        "notes"             => [
                            "address"           => $user->address,
                            "merchant_order_id" => $item_number,
                        ],
                        "theme"             => [
                            "color"             => "{{$this->gs->colors}}"
                        ],
                        "order_id"          => $razorpayOrderId,
                    ];
                    
                    if ($this->displayCurrency !== 'INR')
                    {
                        $data['display_currency']  = $this->displayCurrency;
                        $data['display_amount']    = $displayAmount;
                    }
                    
                    $json = json_encode($data);
                    $displayCurrency = $this->displayCurrency;
                    Session::put('item_number',$sub->user_id); 
                    
        return view( 'front.razorpay-checkout', compact( 'data','displayCurrency','json','notify_url' ) );

 }

    
public function notify(Request $request){

        $success = true;

        
        if (empty($_POST['razorpay_payment_id']) === false)
        {

        
            try
            {

                $attributes = array(
                    'razorpay_order_id' => session('razorpay_order_id'),
                    'razorpay_payment_id' => $_POST['razorpay_payment_id'],
                    'razorpay_signature' => $_POST['razorpay_signature']
                );
        
                $this->api->utility->verifyPaymentSignature($attributes);
            }
            catch(SignatureVerificationError $e)
            {
                $success = false;
            }
        }
        
        if ($success === true)
        {
            
            $razorpayOrder = $this->api->order->fetch(session('razorpay_order_id'));
        
            $order_id = $razorpayOrder['receipt'];
            $transaction_id = $_POST['razorpay_payment_id'];



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


        }else{
            $razorpayOrder = $this->api->order->fetch(session('razorpay_order_id'));
            $order_id = $razorpayOrder['receipt'];
        $payment = UserSubscription::where('user_id','=',$order_id)
            ->orderBy('created_at','desc')->first();
        $payment->delete();

    }
}
    
}