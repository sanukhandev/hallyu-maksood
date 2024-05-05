<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Models\State;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CountryController extends Controller
{
    public function manageCountry()
    {
        return view('admin.country.index');
    }


      //*** JSON Request
      public function datatables()
      {
           $datas = Country::orderBy('id','desc');
           //--- Integrating This Collection Into Datatables
           return DataTables::of($datas)
                              ->addColumn('action', function(Country $data) {
                                  return '<div class="action-list"><a href="' . route('admin-state-index',$data->id) . '"><i class="fas fa-city"></i> Manage State</a></div>';
                              })

                              ->addColumn('status', function(Country $data) {
                                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                $s = $data->status == 1 ? 'selected' : '';
                                $ns = $data->status == 0 ? 'selected' : '';
                                return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-country-status',[ $data->id,1]).'" '.$s.'>Activated</option><option data-val="0" value="'. route('admin-country-status',[ $data->id, 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                            })

                              ->rawColumns(['action','status'])
                              ->toJson();//--- Returning Json Data To Client Side
      }



    //*** GET Request Status
    public function status($id1,$id2)
    {
        Country::findOrFail($id1)->update([
            'status' => $id2
        ]);
    }



    public function country_tax()
    {
        return view('admin.country.tax');
    }


       //*** JSON Request
       public function taxDatatables()
       {
            $datas = Country::where('status',1)->orderBy('id','desc');
            //--- Integrating This Collection Into Datatables
            return DataTables::of($datas)
                               ->addColumn('action', function(Country $data) {
                                   return '<div class="action-list"><a href="' . route('admin-set-tax',$data->id) . '" class="edit"> <i class="fas fa-edit"></i> Set Tax</a></div>';
                               })

                               ->editColumn('tax', function(Country $data) {

                                $tax = $data->tax;

                                return  $tax.' (%)' ;
                            })

                        ->rawColumns(['action','tax'])
                        ->toJson();//--- Returning Json Data To Client Side
       }



    public function setTax($id)
    {   $sign = Currency::where('is_default','=',1)->first();
        $country = Country::findOrFail($id);
        return view('admin.country.set_tax',compact('country','sign'));
    }



    public function updateTax(Request $request,$id)
    {

        $sign = Currency::where('is_default','=',1)->first();
        $country = Country::findOrFail($id);
        $country->tax = $request->tax;
        $country->update();

        if($request->is_state_tax == 1){
            $states = State::where('country_id',$id)->where('status',1)->get();

            foreach($states as $key => $state){
                 $state->update([
                     'tax' => ($request->state_tax[$key])
                 ]);
            }
        }


        $mgs = __('Data Update Successfully.');
        return response()->json($mgs);

    }

}
