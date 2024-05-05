<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Compare,
    Models\Product
};
use Session;

class CompareController extends FrontBaseController
{

    public function compare()
    {

        if (!Session::has('compare')) {
            return view('frontend.compare');
        }
        $oldCompare = Session::get('compare');
        $compare = new Compare($oldCompare);
        $products = $compare->items;
        return view('frontend.compare', compact('products'));
    }

    public function addcompare($id)
    {
        $data[0] = 0;
        $prod = Product::findOrFail($id);
        $oldCompare = Session::has('compare') ? Session::get('compare') : null;
        $compare = new Compare($oldCompare);
        $compare->add($prod, $prod->id);
        Session::put('compare',$compare);
        if($compare->items[$id]['ck'] == 1)
        {
            $data[0] = 1;
        }
        $data[1] = count($compare->items);
        $data['success'] = __('Successfully Added To Compare.');
        $data['error'] = __('Already Added To Compare.');
        return response()->json($data);

    }

    public function removecompare($id)
    {
        $data[0] = 0;
        $oldCompare = Session::has('compare') ? Session::get('compare') : null;
        $compare = new Compare($oldCompare);
        $compare->removeItem($id);
        $data[1] = count($compare->items);
        $data['success'] = __('Successfully Removed From Compare.');
        if (count($compare->items) > 0) {
            Session::put('compare', $compare);
            return response()->json($data);
        } else {
            $data[0] = 1;
            $data['error'] = __('No Product To Compare.');
            Session::forget('compare');
            return response()->json($data);
        }
    }

    public function clearcompare($id)
    {
        Session::forget('compare');
    }

}
