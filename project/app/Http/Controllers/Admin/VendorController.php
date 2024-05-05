<?php

namespace App\Http\Controllers\Admin;

use App\{
    Models\User,
    Models\Withdraw,
    Models\Subscription,
    Classes\GeniusMailer,
    Models\Generalsetting,
    Models\UserSubscription
};
use Illuminate\Http\Request;
use Auth;
use Validator;
use Datatables;
use Carbon\Carbon;

class VendorController extends AdminBaseController
{
	//*** JSON Request
	public function datatables()
	{
	    $datas = User::where('is_vendor','=',2)->orWhere('is_vendor','=',1)->latest('id')->get();
	    //--- Integrating This Collection Into Datatables
	    return Datatables::of($datas)
            ->addColumn('status', function(User $data) {
                $class = $data->is_vendor == 2 ? 'drop-success' : 'drop-danger';
                $s = $data->is_vendor == 2 ? 'selected' : '';
                $ns = $data->is_vendor == 1 ? 'selected' : '';
                return '<div class="action-list"><select class="process select vendor-droplinks '.$class.'">'.
                    '<option value="'. route('admin-vendor-st',['id1' => $data->id, 'id2' => 2]).'" '.$s.'>'.__("Activated").'</option>'.
                    '<option value="'. route('admin-vendor-st',['id1' => $data->id, 'id2' => 1]).'" '.$ns.'>'.__("Deactivated").'</option></select></div>';
                }) 
	            ->addColumn('action', function(User $data) {
	                return '<div class="godropdown"><button class="go-dropdown-toggle"> '.__("Actions").'<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-vendor-secret',$data->id) . '" > <i class="fas fa-user"></i> '.__("Secret Login").'</a><a href="javascript:;" data-href="' . route('admin-vendor-add-subs',$data->id) . '" class="add-subs" data-toggle="modal" data-target="#ad-subscription-modal"> <i class="fas fa-plus"></i> '.__("Add New Plan").'</a><a href="javascript:;" data-href="' . route('admin-vendor-verify',$data->id) . '" class="verify" data-toggle="modal" data-target="#verify-modal"> <i class="fas fa-question"></i> '.__("Ask For Verification").'</a><a href="' . route('admin-vendor-show',$data->id) . '" > <i class="fas fa-eye"></i> '.__("Details").'</a><a data-href="' . route('admin-vendor-edit',$data->id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-edit"></i> '.__("Edit").'</a><a href="javascript:;" class="send" data-email="'. $data->email .'" data-toggle="modal" data-target="#vendorform"><i class="fas fa-envelope"></i> '.__("Send Email").'</a><a href="javascript:;" data-href="' . route('admin-vendor-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> '.__("Delete").'</a></div></div>';
	            }) 
	            ->rawColumns(['status','action'])
	            ->toJson(); //--- Returning Json Data To Client Side
    }
    
    public function index(){
        return view('admin.vendor.index');
    }

    public function withdraws(){
        return view('admin.vendor.withdraws');
    }

	//*** GET Request
  	public function status($id1,$id2)
    {
        $user = User::findOrFail($id1);
        $user->is_vendor = $id2;
        $user->update();
        //--- Redirect Section        
        $msg[0] = __('Status Updated Successfully.');
        return response()->json($msg);      
        //--- Redirect Section Ends    

    }

	//*** GET Request
    public function edit($id)
    {
        $data = User::findOrFail($id);
        return view('admin.vendor.edit',compact('data'));
    }

	//*** GET Request
    public function verify($id)
    {
        $data = User::findOrFail($id);
        return view('admin.vendor.verification',compact('data'));
    }

	//*** POST Request
    public function verifySubmit(Request $request, $id)
    {
        $settings = Generalsetting::find(1);
        $user = User::findOrFail($id);
        $user->verifies()->create(['admin_warning' => 1, 'warning_reason' => $request->details]);

                    if($settings->is_smtp == 1)
                    {
                    $data = [
                        'to' => $user->email,
                        'type' => "vendor_verification",
                        'cname' => $user->name,
                        'oamount' => "",
                        'aname' => "",
                        'aemail' => "",
                        'onumber' => "",
                    ];
                    $mailer = new GeniusMailer();
                    $mailer->sendAutoMail($data);        
                    }
                    else
                    {
                    $headers = "From: ".$settings->from_name."<".$settings->from_email.">";
                    mail($user->email,'Request for verification.','You are requested verify your account. Please send us photo of your passport.Thank You.',$headers);
                    }

        $msg = 'Verification Request Sent Successfully.';
        return response()->json($msg);   
    }

	//*** POST Request
    public function update(Request $request, $id)
    {
	    //--- Validation Section
	        $rules = [
                'shop_name'   => 'unique:users,shop_name,'.$id,
                 ];
            $customs = [
                'shop_name.unique' => 'Shop Name "'.$request->shop_name.'" has already been taken. Please choose another name.'
            ];

         $validator = Validator::make($request->all(), $rules,$customs);
         
         if ($validator->fails()) {
           return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
         }
         //--- Validation Section Ends

        $user = User::findOrFail($id);
        $data = $request->all();
        $user->update($data);
        $msg = 'Vendor Information Updated Successfully.';
        return response()->json($msg);   
    }

	//*** GET Request
    public function show($id)
    {
        $data = User::findOrFail($id);
        return view('admin.vendor.show',compact('data'));
    }
    
    //*** GET Request
    public function secret($id)
    {
        Auth::guard('web')->logout();
        $data = User::findOrFail($id);
        Auth::guard('web')->login($data); 
        return redirect()->route('vendor.dashboard');
    }
    
	//*** GET Request
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->is_vendor = 0;
        $user->is_vendor = 0;
        $user->shop_name = null;
        $user->shop_details= null;
        $user->owner_name = null;
        $user->shop_number = null;
        $user->shop_address = null;
        $user->reg_number = null;
        $user->shop_message = null;
        $user->update();
        if($user->notivications->count() > 0)
        {
            foreach ($user->notivications as $gal) {
                $gal->delete();
            }
        }
        //--- Redirect Section     
        $msg = 'Vendor Deleted Successfully.';
        return response()->json($msg);      
        //--- Redirect Section Ends    
    }

    //*** JSON Request
    public function withdrawdatatables()
    {
        $datas = Withdraw::where('type','=','vendor')->latest('id')->get();
        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)
            ->addColumn('name', function(Withdraw $data) {
                $name = $data->user->name;
                return '<a href="' . route('admin-vendor-show',$data->user->id) . '" target="_blank">'. $name .'</a>';
            }) 
            ->addColumn('email', function(Withdraw $data) {
                $email = $data->user->email;
                return $email;
            }) 
            ->addColumn('phone', function(Withdraw $data) {
                $phone = $data->user->phone;
                return $phone;
            }) 
            ->editColumn('status', function(Withdraw $data) {
                $status = ucfirst($data->status);
                return $status;
            }) 
            ->editColumn('amount', function(Withdraw $data) {
                $sign = $this->curr;
                $amount = $data->amount * $sign->value;
                return \PriceHelper::showAdminCurrencyPrice($amount);
            }) 
            ->addColumn('action', function(Withdraw $data) {
                $action = '<div class="action-list"><a data-href="' . route('admin-vendor-withdraw-show',$data->id) . '" class="view details-width" data-toggle="modal" data-target="#modal1"> <i class="fas fa-eye"></i> Details</a>';
                if($data->status == "pending") {
                    $action .= '<a data-href="' . route('admin-vendor-withdraw-accept',$data->id) . '" data-toggle="modal" data-target="#status-modal1"> <i class="fas fa-check"></i> Accept</a><a data-href="' . route('admin-vendor-withdraw-reject',$data->id) . '" data-toggle="modal" data-target="#status-modal"> <i class="fas fa-trash-alt"></i> Reject</a>';
                }
                $action .= '</div>';
                return $action;
            }) 
            ->rawColumns(['name','action'])
            ->toJson(); //--- Returning Json Data To Client Side
        }

    //*** GET Request       
    public function withdrawdetails($id)
    {
        $sign = $this->curr;
        $withdraw = Withdraw::findOrFail($id);
        return view('admin.vendor.withdraw-details',compact('withdraw','sign'));
    }

    //*** GET Request   
    public function accept($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        $data['status'] = "completed";
        $withdraw->update($data);
        //--- Redirect Section     
        $msg = 'Withdraw Accepted Successfully.';
        return response()->json($msg);      
        //--- Redirect Section Ends   
    }

    //*** GET Request   
    public function reject($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        $account = User::findOrFail($withdraw->user->id);
        $account->current_balance = $account->current_balance + $withdraw->amount + $withdraw->fee;
        $account->update();
        $data['status'] = "rejected";
        $withdraw->update($data);
        //--- Redirect Section     
        $msg = 'Withdraw Rejected Successfully.';
        return response()->json($msg);      
        //--- Redirect Section Ends   
    }

	//*** GET Request
    public function addSubs($id)
    {
        $data = User::findOrFail($id);
        return view('admin.vendor.add-subs',compact('data'));
    }

	//*** POST Request
    public function addSubsStore(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $package = $user->subscribes()->where('status',1)->orderBy('id','desc')->first();
        $subs = Subscription::findOrFail($request->subs_id);
        $settings = Generalsetting::findOrFail(1);
        $today = Carbon::now()->format('Y-m-d');
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
        $user->update();
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
        $sub->status = 1;
        $sub->save();
        if($settings->is_smtp == 1)
        {
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
        }
        else
        {
        $headers = "From: ".$settings->from_name."<".$settings->from_email.">";
        mail($user->email,'Your Vendor Account Activated','Your Vendor Account Activated Successfully. Please Login to your account and build your own shop.',$headers);
        }

        $msg = 'Subscription Plan Added Successfully.';
        return response()->json($msg);   
    }

}