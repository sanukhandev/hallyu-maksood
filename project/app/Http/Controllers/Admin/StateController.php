<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class StateController extends Controller
{
    public function manageState ($country)
    {   $country = Country::findOrFail($country);
        return view('admin.country.state.index',compact('country'));
    }

    //*** JSON Request
    public function datatables($country)
    {
         $datas = State::orderBy('id','desc')->where('country_id',$country)->get();
         //--- Integrating This Collection Into Datatables
         return DataTables::of($datas)
                            ->addColumn('action', function(State $data) {
                                return '<div class="action-list"><a data-href="' . route('admin-state-edit',$data->id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-edit"></i>Edit</a><a href="javascript:;" data-href="' . route('admin-state-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
                            }) 

                            ->editColumn('country_id', function(State $data) {
                                $country = $data->country->country_name ;
                                return  $country;
                            })
                            
                            ->editColumn('tax', function(State $data) {
                                $tax = $data->tax;
                                return  $tax .' (%)' ;
                            })

                            ->addColumn('status', function(State $data) {
                              $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                              $s = $data->status == 1 ? 'selected' : '';
                              $ns = $data->status == 0 ? 'selected' : '';
                              return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-state-status',[ $data->id,1]).'" '.$s.'>Activated</option><option data-val="0" value="'. route('admin-state-status',[ $data->id, 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                          })

                            ->rawColumns(['action','status','country_id'])
                            ->toJson();//--- Returning Json Data To Client Side
    }




    public function create($country)
    {
        $country = Country::findOrFail($country);
        return view('admin.country.state.create',compact('country'));
    }


    public function store(Request $request,$country)
    {
        
         $rules = [
            'state'  => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
     
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $state = new State();
        $state->state = $request->state;
        $state->country_id = $country;
        $state->save();
        $mgs = __('Data Added Successfully.');
        return response()->json($mgs);

    }


    //*** GET Request Status
    public function status($id1,$id2)
    {
        State::findOrFail($id1)->update([
            'status' => $id2
        ]);
    }


    public function edit($id)
    {
        $state = State::findOrFail($id);
        return view('admin.country.state.edit',compact('state'));
    }


    public function update(Request $request , $id)
    {
        $rules = [
            'state'  => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
     
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $state = State::findOrFail($id);
        $state->state = $request->state;
        $state->update();
        $mgs = __('Data Updated Successfully.');
        return response()->json($mgs);
    }


    public function delete($id)
    {
        $state = State::findOrFail($id);
        $state->delete();
        $mgs = __('Data Deleted Successfully.');
        return response()->json($mgs);
    }



}
