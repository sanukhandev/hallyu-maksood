<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\Reward;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RewardController extends Controller
{
    public function index()
    {
        $datas = Reward::get();
        return view('admin.reward.index',compact('datas'));
    }


    public function update(Request $request)
    {
        DB::table('rewards')->delete();
        if($request->order_amount && $request->reward){
            foreach($request->order_amount as $key => $amount){
                $data = new Reward();
                $data->order_amount = $amount;
                $data->reward = $request->reward[$key];
                $data->save();
            }
        }
        $mgs = __('Data Update Successfully');
        return response()->json($mgs);
    }


    public function infoUpdate(Request $request)
    {
        
        $rules = ['reward_point' => 'required|integer|min:1','reward_dolar' => 'required|integer|min:1'];
        $customs = ['reward_dolar.required' => __('Reward dolar field is required.'),'reward_point.required' => __('Reward point field is required.')];
        $validator = Validator::make($request->all(), $rules, $customs);
        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $data = Generalsetting::findOrFail(1);
        $data->reward_dolar = $request->reward_dolar;
        $data->reward_point = $request->reward_point;
        $data->update();
        cache()->forget('generalsettings');
        $mgs = __('Data Update Successfully');
        return response()->json($mgs);

    }

 
}
