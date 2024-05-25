<?php

namespace App\Http\Controllers\Vendor;

use App\{
    Models\Category,
    Models\Subcategory,
    Models\Childcategory
};
use Illuminate\Http\Request;
use Validator;
use Datatables;

class ChildCategoryController extends VendorBaseController
{
    //*** JSON Request
    public function datatables()
    {
         $datas = Childcategory::latest('id')->get();
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->addColumn('category', function(Childcategory $data) {
                                return $data->subcategory->category->name;
                            })
                            ->addColumn('subcategory', function(Childcategory $data) {
                                return $data->subcategory->name;
                            })
                            ->addColumn('status', function(Childcategory $data) {
                                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                $s = $data->status == 1 ? 'selected' : '';
                                $ns = $data->status == 0 ? 'selected' : '';
                                return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('vendor-childcat-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>'.__("Activated").'</option><option data-val="0" value="'. route('vendor-childcat-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>'.__("Deactivated").'</option>/select></div>';
                            })
                            ->addColumn('attributes', function(Childcategory $data) {
                                $buttons = '<div class="action-list"><a data-href="' . route('vendor-attr-createForChildcategory', $data->id) . '" class="attribute" data-toggle="modal" data-target="#attribute"> <i class="fas fa-edit"></i>'.__("Create").'</a>';
                                if ($data->attributes()->count() > 0) {
                                  $buttons .= '<a href="' . route('vendor-attr-manage', $data->id) .'?type=childcategory' . '" class="edit"> <i class="fas fa-edit"></i>'.__("Manage").'</a>';
                                }
                                $buttons .= '</div>';

                                return $buttons;
                            })
                            ->addColumn('action', function(Childcategory $data) {
                                return '<div class="action-list"><a data-href="' . route('vendor-childcat-edit',$data->id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-edit"></i>'.__('Edit').'</a><a href="javascript:;" data-href="' . route('vendor-childcat-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
                            })
                            ->rawColumns(['status', 'attributes','action'])
                            ->toJson(); //--- Returning Json Data To Client Side
    }

    public function index(){
        return view('vendor.childcategory.index');
    }

    //*** GET Request
    public function create()
    {
      	$cats = Category::all();
        return view('vendor.childcategory.create',compact('cats'));
    }

    //*** POST Request
    public function store(Request $request)
    {
        //--- Validation Section
        $rules = [
            'slug' => 'unique:childcategories|regex:/^[a-zA-Z0-9\s-]+$/'
                 ];
        $customs = [
            'slug.unique' => __('This slug has already been taken.'),
            'slug.regex' => __('Slug Must Not Have Any Special Characters.')
                   ];
        $validator = Validator::make($request->all(), $rules, $customs);

        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = new Childcategory();
        $input = $request->all();
        $data->fill($input)->save();
        //--- Logic Section Ends

        //--- Redirect Section
        $msg = __('New Data Added Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    //*** GET Request
    public function edit($id)
    {
    	$cats = Category::all();
        $subcats = Subcategory::all();
        $data = Childcategory::findOrFail($id);
        return view('vendor.childcategory.edit',compact('data','cats','subcats'));
    }

    //*** POST Request
    public function update(Request $request, $id)
    {
        //--- Validation Section
        $rules = [
            'slug' => 'unique:childcategories,slug,'.$id.'|regex:/^[a-zA-Z0-9\s-]+$/'
                 ];
        $customs = [
            'slug.unique' => __('This slug has already been taken.'),
            'slug.regex' => __('Slug Must Not Have Any Special Characters.')
                   ];
        $validator = Validator::make($request->all(), $rules, $customs);

        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = Childcategory::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section
        $msg = __('Data Updated Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }

      //*** GET Request Status
      public function status($id1,$id2)
        {
            $data = Childcategory::findOrFail($id1);
            $data->status = $id2;
            $data->update();
            //--- Redirect Section
            $msg = __('Status Updated Successfully.');
            return response()->json($msg);
            //--- Redirect Section Ends
        }

    //*** GET Request
    public function load($id)
    {
        $subcat = Subcategory::findOrFail($id);
        return view('load.childcategory',compact('subcat'));
    }


    //*** GET Request Delete
    public function destroy($id)
    {
        $data = Childcategory::findOrFail($id);

        if($data->attributes->count()>0)
        {
        //--- Redirect Section
        $msg = 'Remove the Attributes first !';
        return response()->json($msg);
        //--- Redirect Section Ends
        }

        if($data->products->count()>0)
        {
        //--- Redirect Section
        $msg = 'Remove the products first !';
        return response()->json($msg);
        //--- Redirect Section Ends
        }

        $data->delete();
        //--- Redirect Section
        $msg = __('Data Deleted Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }
}
