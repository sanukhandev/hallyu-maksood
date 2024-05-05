<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\{
    Models\Product,
    Models\Wishlist
};


class WishlistController extends UserBaseController
{

    public function wishlists(Request $request)
    {
        $gs = $this->gs;
        $sort = '';
        $pageby = $request->pageby;
        $user = $this->user;
        $wishes = Wishlist::where('user_id','=',$user->id)->pluck('product_id');
        $page_count = isset($pageby) ? $pageby : $gs->wishlist_count;


        if(!empty($request->sort))
        {
            $sort = $request->sort;
            if($sort == "id_desc")
            {
            $wishlists = Product::where('status','=',1)->whereIn('id',$wishes)->latest('id')->paginate($page_count);
            }
            else if($sort == "id_asc")
            {
            $wishlists = Product::where('status','=',1)->whereIn('id',$wishes)->paginate($page_count);
            }
            else if($sort == "price_asc")
            {
            $wishlists = Product::where('status','=',1)->whereIn('id',$wishes)->oldest('price')->paginate($page_count);
            }
            else if($sort == "price_desc")
            {
            $wishlists = Product::where('status','=',1)->whereIn('id',$wishes)->latest('price')->paginate($page_count);
            }
            if($request->ajax())
            {
                return view('front.ajax.wishlist',compact('user','wishlists','sort','pageby'));
            }
            return view('user.wishlist',compact('user','wishlists','sort','pageby'));
        }

        $wishlists = Product::where('status','=',1)->whereIn('id',$wishes)->latest('id')->paginate($page_count);
        if($request->ajax())
        {
            return view('frontend.ajax.wishlist',compact('user','wishlists','sort','pageby'));
        }
        return view('user.wishlist',compact('user','wishlists','sort','pageby'));
    }

    public function addwish($id)
    {
        $user = $this->user;
        $data[0] = 0;
        $ck = Wishlist::where('user_id','=',$user->id)->where('product_id','=',$id)->get()->count();
        if($ck > 0)
        {
            $data['error'] = __('Already Added To The Wishlist.');
            return response()->json($data);
        }
        $wish = new Wishlist();
        $wish->user_id = $user->id;
        $wish->product_id = $id;
        $wish->save();
        $data[0] = 1;
        $data[1] = count($user->wishlists);
        $data['success'] = __('Successfully Added To The Wishlist.');
        return response()->json($data);

    }

    public function removewish($id)
    {
        $user = $this->user;
        $wish = Wishlist::findOrFail($id);
        $wish->delete();
        $data[0] = 1;
        $data[1] = count($user->wishlists);
        $data['success'] = __('Successfully Removed From Wishlist.');
        return response()->json($data);

    }

}
