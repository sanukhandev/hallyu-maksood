<?php

namespace App\Http\Controllers\Admin;


use App\{
    Models\User,
    Models\Subscription,
    Models\UserSubscription
};

use Illuminate\Http\Request;
use Carbon\Carbon;
use Datatables;

class VendorSubscriptionController extends AdminBaseController
{
    //*** GET Request
    public function subsdatatables($status)
    {
         $datas = UserSubscription::whereStatus($status)->latest('id')->get();
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->addColumn('name', function(UserSubscription $data) {
                                $name = isset($data->user->owner_name) ? $data->user->owner_name : __('Removed');
                                return  $name;
                            })

                            ->editColumn('txnid', function(UserSubscription $data) {
                                $txnid = $data->txnid == null ? __('Free') : $data->txnid;
                                return $txnid;
                            }) 
                            ->editColumn('created_at', function(UserSubscription $data) {
                                $date = $data->created_at->diffForHumans();
                                return $date;
                            }) 
                            ->addColumn('action', function(UserSubscription $data) {
                                $status = '';
                                if($data->status == 0){
                                    $class = $data->status == 1 ? 'drop-success' : 'drop-warning';
                                    $s = $data->status == 1 ? 'selected' : '';
                                    $ns = $data->status == 0 ? 'selected' : '';
                                    $status =  '<select class="process select vendor-droplinks '.$class.'">
                                                    <option data-val="1" value="'. route('admin-user-sub-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>
                                                    '.__("Completed").
                                                    '</option>
                                                    <option data-val="0" value="'. route('admin-user-sub-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>
                                                    '.__("Pending").'
                                                    </option>
                                                </select>';                                        
                                }

                                return '<div class="action-list">'.$status.'<a data-href="' . route('admin-vendor-sub',$data->id) . '" class="view details-width" data-toggle="modal" data-target="#modal1"> <i class="fas fa-eye"></i>'.__('Details').'</a></div>';
                            }) 
                            ->rawColumns(['action'])
                            ->toJson(); //--- Returning Json Data To Client Side
    }

	//*** GET Request
    public function subs($slug)
    {
        if($slug == 'completed'){
            return view('admin.vendor.subscriptions');
        }else if($slug == 'pending'){
            return view('admin.vendor.pending-subscriptions');
        }

    }



	//*** GET Request
    public function sub($id)
    {
        $subs = UserSubscription::findOrFail($id);
        return view('admin.vendor.subscription-details',compact('subs'));
    }

	//*** GET Request
    public function status($id1,$id2)
    {
        $sub = UserSubscription::findOrFail($id1);
        $sub->status = $id2;
        $sub->update();

        $user = User::findOrFail($sub->user_id);    
        $package = $user->subscribes()->where('status',1)->orderBy('id','desc')->first();
        $subs = Subscription::findOrFail($sub->subscription_id);
        $today = Carbon::now()->format('Y-m-d');
        $user->is_vendor = 2;

        if(!empty($package))
        {
            if($package->subscription_id == $sub->id)
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
  
        //--- Redirect Section        
        $msg[0] = __('Status Updated Successfully.');
        return response()->json($msg);      
        //--- Redirect Section Ends    

    }

}