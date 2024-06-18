<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\User,
    Models\Order,
    Models\Reply,
    Models\Rating,
    Models\Report,
    Models\Product,
    Models\Comment,
    Models\Currency,
    Models\ProductClick
};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Session;
use Validator;

class ProductDetailsController extends FrontBaseController
{



    // -------------------------------- PRODUCT DETAILS SECTION ----------------------------------------

    public function product(Request $request,$slug)
    {
        $affilate_user = 0;
        $gs = $this->gs;
        if($gs->product_affilate == 1){
            if($request->has('ref')){
                if(!empty($request->ref)){
                    $ref = $request->ref;
                    $user = User::where('affilate_code',$ref)->first();
                    if($user){
                        $affilate_users = array();
                        $ck = false;
                        if(Auth::check()){
                            // Checking whether the affiliate holder is using his own affilate
                            if(Auth::user()->id != $user->id){
                                $affilate_user = $user->id;
                            }
                        }
                        else{
                            $affilate_user = $user->id;
                        }
                    }
                }
            }
        }

        // include brands
//        $productt = Product::where('slug','=',$slug)->firstOrFail();

        $productt = Product::with('brand')->where('slug', '=', $slug)->firstOrFail();

        if($productt->status == 0){
            return response()->view('errors.404')->setStatusCode(404);
        }

        $productt->views+=1;
        $productt->update();

        $curr = $this->curr;

        $product_click =  new ProductClick;
        $product_click->product_id = $productt->id;
        $product_click->date = Carbon::now()->format('Y-m-d');
        $product_click->save();

        if($productt->user_id != 0)
        {
            $vendors = Product::where('status','=',1)->where('user_id','=',$productt->user_id)->where('language_id',$this->language->id)
            ->take($gs->seller_product_count)->get();
        }
        else
        {
            $vendors = Product::where('status','=',1)->where('user_id','=',0)->where('language_id',$this->language->id)
            ->take($gs->seller_product_count)->get();
        }

//        if ($productt->brand_id != null) {
//            $brand_products = Product::where('brand_id', $productt->brand_id)->where('status', 1)->where('language_id', $this->language->id)->get();
//        } else {
//            $brand_products = null;
//        }


        return view('frontend.product',compact('productt','curr','vendors','affilate_user',));

    }

    public function report(Request $request)
    {
            $rules = [
                   'note' => 'max:400',
                    ];
            $customs = [
                   'note.max' => __('Note Must Be Less Than 400 Characters.'),
                       ];
            $validator = Validator::make($request->all(), $rules, $customs);
            if ($validator->fails()) {
              return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }
        $data = new Report;
        $input = $request->all();
        $data->fill($input)->save();
        $msg = __('Report Sent Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends

    }

    public function quick($id)
    {
        $product = Product::findOrFail($id);
        if (Session::has('currency'))
        {
            $curr = Currency::find(Session::get('currency'));
        }
        else
        {
            $curr = Currency::where('is_default','=',1)->first();
        }
        return view('load.quick',compact('product','curr'));

    }

    public function affProductRedirect($slug)
    {
        $product = Product::where('slug','=',$slug)->first();
        return redirect($product->affiliate_link);

    }
    // -------------------------------- PRODUCT DETAILS SECTION ENDS----------------------------------------



    // -------------------------------- PRODUCT COMMENT SECTION ----------------------------------------

        public function comment(Request $request)
        {
            $comment = new Comment;
            $input = $request->all();
            $comment->fill($input)->save();
            $data[0] = $comment->user->photo ? url('assets/images/users/'.$comment->user->photo):url('assets/images/'.$this->gs->user_image);
            $data[1] = $comment->user->name;
            $data[2] = $comment->created_at->diffForHumans();
            $data[3] = $comment->text;
            $data[5] = route('product.comment.delete',$comment->id);
            $data[6] = route('product.comment.edit',$comment->id);
            $data[7] = route('product.reply',$comment->id);
            $data[8] = $comment->user->id;
            $newdata  =  '<li>';
            $newdata .= '<div class="single-comment comment-section">';
            $newdata .= '<div class="left-area">';
            $newdata .= '<img src="'. $data[0] .'" alt="">';
            $newdata .= '<h5 class="name">'. $data[1] .'</h5>';
            $newdata .= '<p class="date">'. $data[2] .'</p>';
            $newdata .= '</div>';
            $newdata .= '<div class="right-area">';
            $newdata .= '<div class="comment-body">';
            $newdata .= '<p>'. $data[3] .'</p>';
            $newdata .= '</div>';
            $newdata .= '<div class="comment-footer">';
            $newdata .= '<div class="links">';
            $newdata .= '<a href="javascript:;" class="comment-link reply mr-2"><i class="fas fa-reply "></i>'. __('Reply') .'</a>';
            $newdata .= '<a href="javascript:;" class="comment-link edit mr-2"><i class="fas fa-edit "></i>'. __('Edit') .'</a>';
            $newdata .= '<a href="javascript:;" data-href="'. $data[5] .'" class="comment-link comment-delete mr-2">';
            $newdata .= '<i class="fas fa-trash"></i>'. __('Delete') .'</a>';
            $newdata .= '</div>';
            $newdata .= '</div>';
            $newdata .= '</div>';
            $newdata .= '</div>';
            $newdata .= '<div class="replay-area edit-area d-none">';
            $newdata .= '<form class="update" action="'. $data[6] .'" method="POST">';
            $newdata .= csrf_field();
            $newdata .= '<textarea placeholder="'. __('Edit Your Comment') .'" name="text" required=""></textarea>';
            $newdata .= '<button type="submit">'. __('Submit') .'</button>';
            $newdata .= '<a href="javascript:;" class="remove">'. __('Cancel') .'</a>';
            $newdata .= '</form>';
            $newdata .= '</div>';
            $newdata .= '<div class="replay-area reply-reply-area d-none">';
            $newdata .= '<form class="reply-form" action="'. $data[7] .'" method="POST">';
            $newdata .= '<input type="hidden" name="user_id" value="'. $data[8] .'">';
            $newdata .= csrf_field();
            $newdata .= '<textarea placeholder="'. __('Write Your Reply') .'" name="text" required=""></textarea>';
            $newdata .= '<button type="submit">'. __('Submit') .'</button>';
            $newdata .= '<a href="javascript:;" class="remove">'. __('Cancel') .'</a>';
            $newdata .= '</form>';
            $newdata .= '</div>';
            $newdata .= '</li>';
            return response()->json($newdata);
        }

        public function commentedit(Request $request,$id)
        {
            $comment =Comment::findOrFail($id);
            $comment->text = $request->text;
            $comment->update();
            return response()->json($comment->text);
        }

        public function commentdelete($id)
        {
            $comment =Comment::findOrFail($id);
            if($comment->replies->count() > 0)
            {
                foreach ($comment->replies as $reply) {
                    $reply->delete();
                }
            }
            $comment->delete();
        }

    // -------------------------------- PRODUCT COMMENT SECTION ENDS ----------------------------------------

    // -------------------------------- PRODUCT REPLY SECTION ----------------------------------------

        public function reply(Request $request,$id)
        {
            $reply = new Reply;
            $input = $request->all();
            $input['comment_id'] = $id;
            $reply->fill($input)->save();
            $data[0] = $reply->user->photo ? url('assets/images/users/'.$reply->user->photo):url('assets/images/'.$this->gs->user_image);
            $data[1] = $reply->user->name;
            $data[2] = $reply->created_at->diffForHumans();
            $data[3] = $reply->text;
            $data[4] = route('product.reply.delete',$reply->id);
            $data[5] = route('product.reply.edit',$reply->id);
            $newdata  = '<div class="single-comment replay-review">';
            $newdata .= '<div class="left-area">';
            $newdata .= '<img src="'. $data[0] .'" alt="">';
            $newdata .= '<h5 class="name">'. $data[1] .'</h5>';
            $newdata .= '<p class="date">'. $data[2] .'</p>';
            $newdata .= '</div>';
            $newdata .= '<div class="right-area">';
            $newdata .= '<div class="comment-body">';
            $newdata .= '<p>'. $data[3] .'</p>';
            $newdata .= '</div>';
            $newdata .= '<div class="comment-footer">';
            $newdata .= '<div class="links">';
            $newdata .= '<a href="javascript:;" class="comment-link reply mr-2"><i class="fas fa-reply "></i>'. __('Reply') .'</a>';
            $newdata .= '<a href="javascript:;" class="comment-link edit mr-2"><i class="fas fa-edit "></i>'. __('Edit') .'</a>';
            $newdata .= '<a href="javascript:;" data-href="'. $data[4] .'" class="comment-link reply-delete mr-2">';
            $newdata .= '<i class="fas fa-trash"></i>'. __('Delete') .'</a>';
            $newdata .= '</div>';
            $newdata .= '</div>';
            $newdata .= '</div>';
            $newdata .= '</div>';
            $newdata .= '<div class="replay-area edit-area d-none">';
            $newdata .= '<form class="update" action="'. $data[5] .'" method="POST">';
            $newdata .= csrf_field();
            $newdata .= '<textarea placeholder="'. __('Edit Your Reply') .'" name="text" required=""></textarea>';
            $newdata .= '<button type="submit">'. __('Submit') .'</button>';
            $newdata .= '<a href="javascript:;" class="remove">'. __('Cancel') .'</a>';
            $newdata .= '</form>';
            $newdata .= '</div>';
            return response()->json($newdata);
        }

        public function replyedit(Request $request,$id)
        {
            $reply = Reply::findOrFail($id);
            $reply->text = $request->text;
            $reply->update();
            return response()->json($reply->text);
        }

        public function replydelete($id)
        {
            $reply =Reply::findOrFail($id);
            $reply->delete();
        }

    // -------------------------------- PRODUCT REPLY SECTION ENDS----------------------------------------


    // ------------------ Rating SECTION --------------------

        public function reviewsubmit(Request $request)
        {
            $ck = 0;
            $orders = Order::where('user_id','=',$request->user_id)->where('status','=','completed')->get();

            foreach($orders as $order)
            {
            $cart = json_decode($order->cart, true);
                foreach($cart['items'] as $product)
                {
                    if($request->product_id == $product['item']['id'])
                    {
                        $ck = 1;
                        break;
                    }
                }
            }
            if($ck == 1)
            {
                $user = Auth::user();
                $prev_reviewer = Rating::where('product_id','=',$request->product_id)->where('user_id','=',$user->id)->first();
                if(isset($prev_reviewer))
                {
                    $input = $request->all();
                    $input['review_date'] = date('Y-m-d H:i:s');
                    $prev_reviewer->update($input);
                    $data = __('Your Rating Submitted Successfully.');
                    return response()->json($data);
                }
                $Rating = new Rating;
                $Rating->fill($request->all());
                $Rating['review_date'] = date('Y-m-d H:i:s');
                $Rating->save();
                $data = __('Your Rating Submitted Successfully.');
                return response()->json($data);
            }
            else{
                return response()->json(array('errors' => [ 0 => __('Buy This Product First') ]));
            }
        }

        public function reviews($id){
            $productt = Product::find($id);
            return view('load.reviews',compact('productt','id'));
        }

        public function sideReviews($id){
            $productt = Product::find($id);
            return view('load.side-load',compact('productt'));
        }

    // ------------------ Rating SECTION ENDS --------------------


}
