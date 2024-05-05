<?php

namespace App\Http\Controllers\Vendor;

use App\Models\SocialLink;
use Illuminate\Http\Request;
use Datatables;

class SocialLinkController extends VendorBaseController
{

    //*** JSON Request
    public function datatables()
    {
         $datas = $this->user->sociallinks()->latest('id')->get();
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->addColumn('status', function(SocialLink $data) {
                                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                $s = $data->status == 1 ? 'selected' : '';
                                $ns = $data->status == 0 ? 'selected' : '';
                                return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('vendor-sociallink-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>'.__('Activated').'</option><<option data-val="0" value="'. route('vendor-sociallink-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>'.__('Deactivated').'</option>/select></div>';
                            })
                            ->addColumn('action', function(SocialLink $data) {
                                return '<div class="action-list"><a href="' . route('vendor-sociallink-edit',$data->id) . '"s> <i class="fas fa-edit"></i>'.__('Edit').'</a><a href="javascript:;" data-href="' . route('vendor-sociallink-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
                            })
                            ->rawColumns(['status', 'action']) 
                            ->toJson();//--- Returning Json Data To Client Side
    }

    public function index(){
        return view('vendor.sociallink.index');
    }

    public function create(){
        return view('vendor.sociallink.create');
    }

    //*** POST Request
    public function store(Request $request)
    {
        //--- Logic Section
        $data = new SocialLink;
        $input = $request->all();
        $input['user_id'] = $this->user->id;
        $data->fill($input)->save();
        //--- Logic Section Ends

        //--- Redirect Section  
        $msg = __('New Data Added Successfully.').'<a href="'.route("vendor-sociallink-index").'">'.__("View Lists").'</a>';
        return response()->json($msg);      
        //--- Redirect Section Ends  
    }

    //*** GET Request
    public function edit($id)
    {
        $data = SocialLink::findOrFail($id);
        return view('vendor.sociallink.edit',compact('data'));
    }

    //*** POST Request
    public function update(Request $request, $id)
    {
        //--- Logic Section
        $data = SocialLink::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section          
        $msg = __('Data Updated Successfully.').'<a href="'.route("vendor-sociallink-index").'">'.__("View Lists").'</a>';
        return response()->json($msg);    
        //--- Redirect Section Ends  

    }

    //*** GET Request
    public function status($id1,$id2)
    {
        $data = SocialLink::findOrFail($id1);
        if($data->user_id == $this->user->id)
        {
            $data->status = $id2;
            $data->update();
            //--- Redirect Section
            $msg = __('Status Updated Successfully.');
            return response()->json($msg);
            //--- Redirect Section Ends
        }
    }

    //*** GET Request
    public function destroy($id)
    {
        $data = SocialLink::findOrFail($id);
        if($data->user_id == $this->user->id)
        {
            $data->delete();
            //--- Redirect Section     
            $msg = __('Data Deleted Successfully.');
            return response()->json($msg);      
            //--- Redirect Section Ends   
        }
    }
}
