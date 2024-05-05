<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArrivalSection;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Validator;

class ArrivalsectionController extends Controller
{
    public function datatables()
    {
         $datas = ArrivalSection::latest('id')->get();
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->editColumn('photo', function(ArrivalSection $data) {
                                $photo = $data->photo ? url('assets/images/arrival/'.$data->photo):url('assets/images/noimage.png');
                                return '<img src="' . $photo . '" alt="Image">';
                            })
                            ->editColumn('title', function(ArrivalSection $data) {
                                $title = mb_strlen(strip_tags($data->title),'UTF-8') > 250 ? mb_substr(strip_tags($data->title),0,250,'UTF-8').'...' : strip_tags($data->title);
                                return  $title;
                            })
                            ->editColumn('header', function(ArrivalSection $data) {
                                $header = mb_strlen(strip_tags($data->header),'UTF-8') > 250 ? mb_substr(strip_tags($data->header),0,250,'UTF-8').'...' : strip_tags($data->header);
                                return  $header;
                            })
                            ->addColumn('status', function(ArrivalSection $data) {
                                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                $s = $data->status == 1 ? 'selected' : '';
                                $ns = $data->status == 0 ? 'selected' : '';
                                return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-arrival-status',[ $data->id,1]).'" '.$s.'>Activated</option><option data-val="0" value="'. route('admin-arrival-status',[ $data->id, 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                            })
                            ->addColumn('action', function(ArrivalSection $data) {
                                return '<div class="action-list"><a href="' . route('admin-arrival-edit',$data->id) . '"> <i class="fas fa-edit"></i>'.__('Edit').'</a><a href="javascript:;" data-href="' . route('admin-arrival-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
                            })
                            ->rawColumns(['photo', 'action','status'])
                            ->toJson();
    }
    public function index(){
        return view('admin.arrival.index');
    }
    public function create(){
        return view('admin.arrival.create');
    }

    public function store(Request $request)
    {
        //--- Validation Section

        $rules = [
               'photo'      => 'required|mimes:jpeg,jpg,png,svg',
                ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Logic Section
        $data = new ArrivalSection();
        $input = $request->all();
        if ($file = $request->file('photo'))
         {
            $name = \PriceHelper::ImageCreateName($file);
            $file->move('assets/images/arrival',$name);
            $input['photo'] = $name;
        }
        $data->fill($input)->save();
        //--- Logic Section Ends

        //--- Redirect Section
        $msg = __('New Data Added Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }
    public function edit($id)
    {
        $data = ArrivalSection::findOrFail($id);
        return view('admin.arrival.edit',compact('data'));
    }

    public function status($id1,$id2)
    {
        ArrivalSection::findOrFail($id1)->update([
            'status' => $id2
        ]);
    }

    public function update(Request $request, $id)
    {
        //--- Validation Section
        $rules = [
               'photo'      => 'mimes:jpeg,jpg,png,svg',
                ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = ArrivalSection::findOrFail($id);
        $input = $request->all();
            if ($file = $request->file('photo'))
            {
                $name = \PriceHelper::ImageCreateName($file);
                $file->move('assets/images/arrival',$name);
                if($data->photo != null)
                {
                    if (file_exists(public_path().'/assets/images/arrival/'.$data->photo)) {
                        unlink(public_path().'/assets/images/arrival/'.$data->photo);
                    }
                }
            $input['photo'] = $name;
            }
        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section
        $msg = __('Data Updated Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    public function destroy($id)
    {
        $data = ArrivalSection::findOrFail($id);
        //If Photo Doesn't Exist
        if($data->photo == null){
            $data->delete();
            //--- Redirect Section
            $msg = __('Data Deleted Successfully.');
            return response()->json($msg);
            //--- Redirect Section Ends
        }
        //If Photo Exist
        if (file_exists(public_path().'/assets/images/arrival/'.$data->photo)) {
            unlink(public_path().'/assets/images/arrival/'.$data->photo);
        }
        $data->delete();
        //--- Redirect Section
        $msg = __('Data Deleted Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }
}
