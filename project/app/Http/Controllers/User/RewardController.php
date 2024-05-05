<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RewardController extends UserBaseController
{
    public function rewards()
    {
        $curr = Currency::where('is_default','=',1)->first();
        $user = Auth::user();
        $datas = Transaction::where('type','reward')->where('user_id',$user->id)->orderby('id','desc')->get();
        return view('user.reward.index',compact('user','datas','curr'));
    }

    public function convert()
    {
        $curr = Currency::where('is_default','=',1)->first();
        $user = Auth::user();
        return view('user.reward.convert',compact('user','curr'));
    }


    public function convertSubmit(Request $request)
    {
        $curr = Currency::where('is_default','=',1)->first();
        $user = Auth::user();
        $gs = Generalsetting::find(1);

        $rules =
        [
            'reward_point' => 'required|integer|max:'.$user->reward.'|min:'.$gs->reward_point
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $dolar = ($request->reward_point / $gs->reward_point)  * $gs->reward_dolar;

        $user->reward = $user->reward - $request->reward_point;
        $user->balance = $user->balance + $dolar;
        $user->update();
        $trans =  new Transaction();
        $trans->user_id = $user->id;
        $trans->reward_point = $request->reward_point;
        $trans->reward_dolar = $dolar;
        $trans->type = 'reward';
        $trans->save();

        $mgs = __('Your Wallet Balance Added ' . ' : $'. round($dolar * $curr->value ,2));
        return response()->json($mgs);


    }
}
