<?php

namespace App\Http\Controllers\Admin;

use App\{Models\Brands};
use Illuminate\{
    Support\Str,
    Http\Request
};


use DB;
use Auth;
use Image;
use Validator;
use Datatables;
class BrandsController extends AdminBaseController
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
                return '<div class="action-list"><select class="process select droplinks ' . $class . '"><option data-val="1" value="' . route('admin-brand-status', ['id1' => $data->brand_id, 'id2' => 1]) . '" ' . $s . '>' . __("Activated") . '</option><option data-val="0" value="' . route('admin-brand-status', ['id1' => $data->brand_id, 'id2' => 0]) . '" ' . $ns . '>' . __("Deactivated") . '</option>/select></div>';
            })
            ->addColumn('logo', function (Brands $data) {
                return '<div class="manage-list-image"><img src="' . asset($data->brand_logo) . '"></div>';
            })
            ->addColumn('action', function (Brands $data) {
                return '<div class="action-list"><a data-href="' . route('admin-brand-edit', $data->brand_id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-edit"></i>' . __('Edit') . '</a><a href="javascript:;" data-href="' . route('admin-brand-delete', $data->brand_id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
            })
            ->rawColumns(['status', 'action'])
            ->toJson(); //--- Returning Json Data To Client Side
    }

    public function status($id1, $id2)
    {
        $data = Brands::findOrFail($id1);
        $data->brand_is_active = $id2;
        $data->update();
    }

    public function index()
    {
        return view('admin.brands.index');
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    //*** POST Request
    public function store(Request $request)
    {
        //--- Validation Section
        $rules = [
            'brand_logo' => 'required',
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
        return response()->json('New Data Added Successfully.');
    }

    public function uploadImage($request)
    {

        $image = $request->brand_logo;
        list($type, $image) = explode(';', $image);
        list(, $image)      = explode(',', $image);
        $image = base64_decode($image);
        $image_name = time().Str::random(8).'.png';
        // create brands directory if not exists
        if (!file_exists('assets/images/brands')) {
            mkdir('assets/images/brands', 0777, true);
        }
        $path = 'assets/images/brands/'.$image_name;
        file_put_contents($path, $image);
        return $path;
    }



}

