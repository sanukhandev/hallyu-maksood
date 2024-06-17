<?php

namespace App\Http\Controllers\Vendor;

use App\{
    Models\Brands
};
use Illuminate\{
    Support\Str,
    Http\Request
};
use Session;
use Validator;
use Datatables;
class BrandsController extends VendorBaseController
{
    //*** JSON Request
    public function datatables()
    {
        $datas = Brands::latest('brand_id')->get();
        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)
            ->addColumn('status', function (Brands $data) {
                $class = $data->brand_is_active == 1 ? 'drop-success' : 'drop-danger';
                $s = $data->brand_is_active == 1 ? 'selected' : '';
                $ns = $data->brand_is_active == 0 ? 'selected' : '';
                return '<div class="action-list"><select class="process select droplinks ' . $class . '"><option data-val="1" value="' . route('vendor-brand-status', ['id1' => $data->brand_id, 'id2' => 1]) . '" ' . $s . '>' . __("Activated") . '</option><option data-val="0" value="' . route('vendor-brand-status', ['id1' => $data->brand_id, 'id2' => 0]) . '" ' . $ns . '>' . __("Deactivated") . '</option>/select></div>';
            })
            ->addColumn('action', function (Brands $data) {
                return '<div class="action-list"><a data-href="' . route('vendor-brand-edit', $data->brand_id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-edit"></i>' . __('Edit') . '</a><a href="javascript:;" data-href="' . route('vendor-brand-delete', $data->brand_id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
            })
            ->rawColumns(['status', 'action'])
            ->toJson(); //--- Returning Json Data To Client Side
    }

    public function index()
    {
        return view('vendor.brand.index');
    }

    public function create()
    {
        return view('vendor.brand.create');
    }

    //*** POST Request
    public function store(Request $request)
    {
        //--- Validation Section
        $rules = [
            'brand_logo' => 'mimes:jpeg,jpg,png,svg',
            'brand_name' => 'required',
            'brand_description' => 'required',
            'brand_country' => 'required',
            'brand_website' => 'required',
            'brand_is_active' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        //--- Logic Section

        $data = new Brands();
        $input = $request->all();
        $input['brand_logo'] = $this->uploadImage($request);
        $data->fill($input)->save();
    }



}

