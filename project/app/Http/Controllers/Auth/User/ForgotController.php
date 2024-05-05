<?php

namespace App\Http\Controllers\Auth\User;

use App\{
    Models\User,
    Classes\GeniusMailer,
    Http\Controllers\Controller
};

use Illuminate\{
  Http\Request,
  Support\Facades\Hash
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ForgotController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index(){
        if (Session::has('language'))
            {
                $langg = DB::table('languages')->find(Session::get('language'));
            }
            else
            {
                $langg = DB::table('languages')->where('is_default','=',1)->first();
            }
      return view('frontend.forget',compact('langg'));

    }

    public function forgot(Request $request)
    {
      $input =  $request->all();
      if (User::where('email', '=', $request->email)->count() > 0) {
      // user found
      $admin = User::where('email', '=', $request->email)->first();
      $token = md5(time().$admin->name.$admin->email);
      $input['email_token'] = $token;
      $admin->update($input);
      $subject = "Reset Password Request";
      $msg = "Please click this link : ".'<a href="'.route('user.change.token',$token).'">'.route('user.change.token',$token).'</a>'.' to change your password.';

      $data = [
        'to' => $request->email,
        'subject' => $subject,
        'body' => $msg,
      ];

      $mailer = new GeniusMailer();
      $mailer->sendCustomMail($data);

      return response()->json(__('Verification Link Sent Successfully!. Please Check your email.'));
      }
      else{
      // user not found
      return response()->json(array('errors' => [ 0 => __('No Account Found With This Email.') ]));
      }
    }

    public function showChangePassForm($token)
    {
      if($token){
        if( User::where('email_token', $token)->exists() ){
          return view('user.changepass',compact('token'));
        }
      }
    }

    public function changepass(Request $request)
    {
        $token = $request->file_token;
        $admin =  User::where('email_token', $token)->first();
        if($admin){
          if ($request->cpass){
            if (Hash::check($request->cpass, $admin->password)){
                if ($request->newpass == $request->renewpass){
                    $input['password'] = Hash::make($request->newpass);
                }else{
                    return response()->json(array('errors' => [ 0 => __('Confirm password does not match.') ]));
                }
            }else{
                return response()->json(array('errors' => [ 0 => __('Current password does not match.') ]));
            }
        }
        $admin->email_token = null;
        $admin->update($input);

        $msg = __('Successfully changed your password.').'<a href="'.route('front.index').'?forgot=success"> '.__('Login Now').'</a>';
        return response()->json($msg);
        }else{
          return response()->json(array('errors' => [ 0 => __('Invalid Token.') ]));
        }
    }

}
