<?php
/**
 * Created by PhpStorm.
 * User: ShaOn
 * Date: 11/29/2018
 * Time: 12:49 AM
 */

namespace App\Classes;

use App\{
    Models\EmailTemplate,
    Models\Generalsetting
};

use DB;
use Config;
use Illuminate\Support\Facades\Mail;


class GeniusMailer
{
    public $owner;
    public function __construct()
    {
        $username = explode('/',request()->path());
        if(DB::table('admins')->where('username',$username[0])->where('role','Owner')->exists()){
            $this->owner = DB::table('admins')->where('username',$username[0])->where('role','Owner')->first();
            $gs = Generalsetting::whereRegisterId($this->owner->id)->first();
            Config::set('mail.driver', $gs->mail_driver);
            Config::set('mail.host', $gs->mail_host);
            Config::set('mail.port', $gs->mail_port);
            Config::set('mail.encryption', $gs->mail_encryption);
            Config::set('mail.username', $gs->mail_user);
            Config::set('mail.password', $gs->mail_pass);

        }
        else{
            $gs = Generalsetting::findOrFail(1);

            Config::set('mail.driver', $gs->mail_driver);
            Config::set('mail.host', $gs->mail_host);
            Config::set('mail.port', $gs->mail_port);
            Config::set('mail.encryption', $gs->mail_encryption);
            Config::set('mail.username', $gs->mail_user);
            Config::set('mail.password', $gs->mail_pass);
        }

    }

    public function sendAutoMail(array $mailData)
    {

        if(!empty($this->owner)){
            $setup = Generalsetting::whereRegisterId($this->owner->id)->first();

            $temp = EmailTemplate::whereRegisterId($this->owner->id)->where('email_type','=',$mailData['type'])->first();
        }else{
            $setup = Generalsetting::find(1);

            $temp = EmailTemplate::whereRegisterId(0)->where('email_type','=',$mailData['type'])->first();
        }


        $body = preg_replace("/{customer_name}/", $mailData['cname'] ,$temp->email_body);
        $body = preg_replace("/{order_amount}/", $mailData['oamount'] ,$body);
        $body = preg_replace("/{admin_name}/", $mailData['aname'] ,$body);
        $body = preg_replace("/{admin_email}/", $mailData['aemail'] ,$body);
        $body = preg_replace("/{order_number}/", $mailData['onumber'] ,$body);
        $body = preg_replace("/{website_title}/", $setup->title ,$body);

        $data = [
            'email_body' => $body
        ];

        if($setup->is_smtp == 1){

            $objDemo = new \stdClass();
            $objDemo->to = $mailData['to'];
            $objDemo->from = $setup->from_email;
            $objDemo->title = $setup->from_name;
            $objDemo->subject = $temp->email_subject;
    
        
                Mail::send('admin.email.mailbody',$data, function ($message) use ($objDemo) {
                    $message->from($objDemo->from,$objDemo->title);
                    $message->to($objDemo->to);
                    $message->subject($objDemo->subject);
                });
            
           
        }   
        
        else {
            $to = $mailData['to'];
            $subject = $temp->email_subject;
            $from = $setup->from_email;
             
            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
             
            // Create email headers
            $headers .= 'From: '.$from."\r\n".
            'Reply-To: '.$from."\r\n" .
            'X-Mailer: PHP/' . phpversion();

            // Sending email
            mail($to, $subject, $data['email_body'], $headers);            
        }
    }

    public function sendCustomMail(array $mailData)
    {
        if(!empty($this->owner)){
            $setup = Generalsetting::whereRegisterId($this->owner->id)->first();

        }else{
            $setup = Generalsetting::find(1);

        }
        $data = ['email_body' => $mailData['body']];

        if($setup->is_smtp == 1){

            $objDemo = new \stdClass();
            $objDemo->to = $mailData['to'];
            $objDemo->from = $setup->from_email;
            $objDemo->title = $setup->from_name;
            $objDemo->subject = $mailData['subject'];


            try{
                Mail::send('admin.email.mailbody',$data, function ($message) use ($objDemo) {
                    $message->from($objDemo->from,$objDemo->title);
                    $message->to($objDemo->to);
                    $message->subject($objDemo->subject);
                });
            }
            catch (\Exception $e){
                //die("Not sent");
            }
        }
        else{
            $to = $mailData['to'];
            $subject = $mailData['subject'];
            $from = $setup->from_email;
             
            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
             
            // Create email headers
            $headers .= 'From: '.$from."\r\n".
            'Reply-To: '.$from."\r\n" .
            'X-Mailer: PHP/' . phpversion();

            // Sending email
            mail($to, $subject, $data['email_body'], $headers);
    
        }

        return true;
    }

}