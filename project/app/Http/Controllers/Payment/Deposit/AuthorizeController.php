<?php

namespace App\Http\Controllers\Payment\Deposit;

use App\{
    Models\Deposit,
    Models\Transaction,
    Classes\GeniusMailer,
    Models\PaymentGateway
};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthorizeController extends DepositBaseController
{

    public function store(Request $request){

        $data = PaymentGateway::whereKeyword('authorize.net')->first();
        $user = $this->user;
        $item_amount = (string)$request->amount;
        $item_name = "Deposit Via  Authorize.net";
        $item_number = Str::random(4).time();
       
        $curr = $this->curr;
        $supported_currency = json_decode($data->currency_id,true);
        if(!in_array($curr->id,$supported_currency)){
            return redirect()->back()->with('unsuccess',__('Invalid Currency For Authorize Payment.'));
        }
        
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

                        $user->balance = $user->balance + ($request->amount / $this->curr->value);
                        $user->mail_sent = 1;
                        $user->save();
      
                        $deposit = new Deposit;
                        $deposit->user_id = $user->id;
                        $deposit->currency = $this->curr->sign;
                        $deposit->currency_code = $this->curr->name;
                        $deposit->currency_value = $this->curr->value;
                        $deposit->amount = $request->amount / $this->curr->value;
                        $deposit->method = 'Authorize.net';
                        $deposit->txnid = $tresponse->getTransId();
                        $deposit->status = 1;
                        $deposit->save();
      
                        // store in transaction table
                        if ($deposit->status == 1) {
                          $transaction = new Transaction;
                          $transaction->user_id = $deposit->user_id;
                          $transaction->amount = $deposit->amount;
                          $transaction->user_id = $deposit->user_id;
                          $transaction->currency_sign = $deposit->currency;
                          $transaction->currency_code = $deposit->currency_code;
                          $transaction->currency_value= $deposit->currency_value;
                          $transaction->method = $deposit->method;
                          $transaction->txnid = $deposit->txnid;
                          $transaction->details = 'Payment Deposit';
                          $transaction->type = 'plus';
                          $transaction->save();
                        }
      

                          $data = [
                              'to' => $user->email,
                              'type' => "wallet_deposit",
                              'cname' => $user->name,
                              'damount' => $deposit->amount,
                              'wbalance' => $user->balance,
                              'oamount' => "",
                              'aname' => "",
                              'aemail' => "",
                              'onumber' => "",
                          ];
                          $mailer = new GeniusMailer();
                          $mailer->sendAutoMail($data);


                        return redirect()->route('user-dashboard')->with('success',__('Balance has been added to your account.'));

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