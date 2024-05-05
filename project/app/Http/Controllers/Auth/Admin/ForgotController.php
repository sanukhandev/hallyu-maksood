<?php

namespace App\Http\Controllers\Auth\Admin;

use App\{
    Models\Admin,
    Classes\GeniusMailer,
    Http\Controllers\Controller
};

use Illuminate\{
  Http\Request,
  Support\Facades\Hash
};

class ForgotController extends Controller
{
    public function __construct()
    {
      $this->middleware('guest:admin');
    }

    public function showForm()
    {
      return view('admin.forgot');
    }

    public function forgot(Request $request)
    {
      $input =  $request->all();
      if (Admin::where('email', '=', $request->email)->count() > 0) {
      // user found
      $user = Admin::where('email', '=', $request->email)->first();
      $token = md5(time().$user->name.$user->email);
      $input['email_token'] = $token;
      $user->update($input);
      $subject = "Reset Password Request";
      $msg = "Please click this link : ".'<a href="'.route('admin.change.token',$token).'">'.route('admin.change.token',$token).'</a>'.' to change your password.';

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
        if( Admin::where('email_token', $token)->exists() ){
          return view('admin.changepass',compact('token'));  
        }
      }
    }

    public function changepass(Request $request)
    {
        $token = $request->file_token;
        $user =  Admin::where('email_token', $token)->first();

        if($user){
          if ($request->cpass){
            if (Hash::check($request->cpass, $user->password)){
                if ($request->newpass == $request->renewpass){
                    $input['password'] = Hash::make($request->newpass);
                }else{
                    return response()->json(array('errors' => [ 0 => __('Confirm password does not match.') ]));
                }
            }else{
                return response()->json(array('errors' => [ 0 => __('Current password does not match.') ]));
            }
        }

        $user->email_token = null;
        $user->update($input);

        $msg = __('Successfully changed your password.').'<a href="'.route('admin.login').'"> '.__('Login Now').'</a>';
        return response()->json($msg);
        }else{
          return response()->json(array('errors' => [ 0 => __('Invalid Token.') ]));
        }
    }
}