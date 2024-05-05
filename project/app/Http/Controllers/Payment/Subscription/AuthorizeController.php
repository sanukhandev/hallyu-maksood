<?php

namespace App\Http\Controllers\Payment\Subscription;

use App\{
    Models\Subscription,
    Classes\GeniusMailer,
    Models\PaymentGateway,
    Models\UserSubscription
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthorizeController extends SubscriptionBaseController
{

    public function store(Request $request){

        $this->validate($request, [
        'shop_name'   => 'unique:users',
        ],[ 
            'shop_name.unique' => __('This shop name has already been taken.')
        ]);

        $subs = Subscription::findOrFail($request->subs_id);
        $data = PaymentGateway::whereKeyword('authorize.net')->first();
        $user = $this->user;

        $item_amount = $subs->price * $this->curr->value;
        $curr = $this->curr;

        $supported_currency = json_decode($data->currency_id,true);
        if(!in_array($curr->id,$supported_currency)){
            return redirect()->back()->with('unsuccess',__('Invalid Currency For Authorize Payment.'));
        }
        
        $package = $user->subscribes()->where('status',1)->orderBy('id','desc')->first();
        $today = Carbon::now()->format('Y-m-d');

        $item_name = $subs->title." Plan";
        $item_number = Str::random(4).time();

        $input = $request->all();  
        $user->is_vendor = 2;


        $validator = \Validator::make($request->all(),[
                        'cardNumber' => 'required',
                        'cardCode' => 'required',
                        'month' => 'required',
                        'year' => 'required',
                    ]);

        if ($validator->passes()) {
        /* Create a merchantAuthenticationType object with authentication details retrieved from the constants file */
            $paydata = $data->convertAutoData();
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($paydata['login_id']);
            $merchantAuthentication->setTransactionKey($paydata['txn_key']);

            // Set the transaction's refId
            $refId = 'ref' . time();

            // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($request->cardNumber);
            $year = $request->year;
            $month = $request->month;
            $creditCard->setExpirationDate($year.'-'.$month);
            $creditCard->setCardCode($request->cardCode);

            // Add the payment data to a paymentType object
            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);
        
            // Create order information
            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($item_number);
            $order->setDescription($item_name);

            // Create a TransactionRequestType object and add the previous objects to it
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction"); 
            $transactionRequestType->setAmount($item_amount);
            $transactionRequestType->setOrder($order);
            $transactionRequestType->setPayment($paymentOne);
            // Assemble the complete transaction request
            $requestt = new AnetAPI\CreateTransactionRequest();
            $requestt->setMerchantAuthentication($merchantAuthentication);
            $requestt->setRefId($refId);
            $requestt->setTransactionRequest($transactionRequestType);
        
            // Create the controller and get the response
            $controller = new AnetController\CreateTransactionController($requestt);
            if($paydata['sandbox_check'] == 1){
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            }
            else {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);                
            }

            if ($response != null) {
                // Check to see if the API request was successfully received and acted upon
                if ($response->getMessages()->getResultCode() == "Ok") {
                    // Since the API request was successful, look for a transaction response
                    // and parse it to display the results of authorizing the card
                    $tresponse = $response->getTransactionResponse();
                
                    if ($tresponse != null && $tresponse->getMessages() != null) {

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
                        $sub->method = 'Authorize.net';
                        $sub->txnid = $tresponse->getTransId();
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

                    } else {
                        return back()->with('unsuccess', __('Payment Failed.'));
                    }
                    // Or, print errors if the API request wasn't successful
                } else {
                    return back()->with('unsuccess', __('Payment Failed.'));
                }      
            } else {
                return back()->with('unsuccess', __('Payment Failed.'));
            }

        }
        return back()->with('unsuccess', __('Invalid Payment Details.'));
    }
}