<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Blog,
    Models\Product,
    Models\Subscriber,
    Models\BlogCategory,
    Classes\GeniusMailer,
};
use App\Models\ArrivalSection;
use App\Models\Rating;
use Illuminate\{
    Http\Request,
    Support\Facades\DB,
    Support\Facades\Session
};
use Artisan;
use Illuminate\Support\Facades\Validator;

class FrontendController extends FrontBaseController
{

// LANGUAGE SECTION

public function language($id)
{

    Session::put('language', $id);
    return redirect()->route('front.index');
}

// LANGUAGE SECTION ENDS

// CURRENCY SECTION

public function currency($id)
{

    if (Session::has('coupon')) {
        Session::forget('coupon');
        Session::forget('coupon_code');
        Session::forget('coupon_id');
        Session::forget('coupon_total');
        Session::forget('coupon_total1');
        Session::forget('already');
        Session::forget('coupon_percentage');
    }
    Session::put('currency', $id);
    cache()->forget('session_currency');
    return redirect()->back();
}

// CURRENCY SECTION ENDS

    // -------------------------------- HOME PAGE SECTION ----------------------------------------

    // Home Page Display

	public function index(Request $request)
	{



        $gs = $this->gs;
        $data['ps'] = $this->ps;
         if(!empty($request->reff))
         {
            $affilate_user = DB::table('users')
                            ->where('affilate_code','=',$request->reff)
                            ->first();
            if(!empty($affilate_user))
            {
                if($gs->is_affilate == 1)
                {
                    Session::put('affilate', $affilate_user->id);
                    return redirect()->route('front.index');
                }
            }
         }
         if(!empty($request->forgot))
         {
            if($request->forgot == 'success'){
                return redirect()->guest('/')->with('forgot-modal',__('Please Login Now !'));
            }
         }


        $data['sliders'] = DB::table('sliders')
                            ->where('language_id',$this->language->id)
                            ->get();

         dd($data['sliders']);



        $data['arrivals']=ArrivalSection::where('status',1)->get();
        $data['products']=Product::get();
        $data['ratings']=Rating::get();


	    return view('frontend.index',$data);
	}

    // Home Page Ajax Display

    public function extraIndex()
    {
        $gs = $this->gs;
        $data['hot_products'] = Product::with('user')->whereStatus(1)->whereHot(1)
        ->home($this->language->id)
        ->take($gs->hot_count)
        ->with(['user','category'])
        ->get();

            $data['latest_products'] = Product::with('user')->whereStatus(1)->whereLatest(1)
            ->home($this->language->id)
            ->take($gs->new_count)
            ->with(['user','category'])
            ->get();

        $data['sale_products'] = Product::with('user')->whereStatus(1)->whereSale(1)
            ->home($this->language->id)
            ->take($gs->sale_count)
            ->with(['user','category'])
            ->get();

        $data['best_products'] = Product::with('user')->whereStatus(1)->whereBest(1)
            ->home($this->language->id)
            ->take($gs->best_seller_count)
            ->with(['user','category'])
            ->get();

        $data['popular_products'] = Product::with('user')->whereStatus(1)->whereFeatured(1)
                ->home($this->language->id)
                ->take($gs->popular_count)
                ->with(['user','category'])
                ->get();

        $data['top_products'] = Product::with('user')->whereStatus(1)->whereTop(1)
            ->home($this->language->id)
            ->take($gs->top_rated_count)
            ->with(['user','category'])
            ->get();

        $data['big_products'] = Product::with('user')->whereStatus(1)->whereBig(1)
            ->home($this->language->id)
            ->take($gs->big_save_count)
            ->with(['user','category'])
            ->get();

        $data['trending_products'] = Product::with('user')->whereStatus(1)->whereTrending(1)
                ->home($this->language->id)
                ->take($gs->trending_count)
                ->with(['user','category'])
                ->get();

        $data['flash_products'] = Product::with('user')->whereStatus(1)->whereIsDiscount(1)
            ->where('discount_date', '>=', date('Y-m-d'))
            ->home($this->language->id)
           ->with(['user','category'])
            ->latest()->first();


        $data['blogs'] = Blog::where('language_id',$this->language->id)->latest()->take(2)->get();
        $data['ps'] = $this->ps;


        return view('partials.theme.extraindex',$data);
    }

    // -------------------------------- HOME PAGE SECTION ENDS ----------------------------------------

    // -------------------------------- BLOG SECTION ----------------------------------------

	public function blog(Request $request)
	{

        if(DB::table('pagesettings')->first()->blog == 0){
            return redirect()->back();
        }


        // BLOG TAGS
        $tags = null;
        $tagz = '';
        $name = Blog::where('language_id',$this->language->id)->pluck('tags')->toArray();
        foreach($name as $nm)
        {
            $tagz .= $nm.',';
        }
        $tags = array_unique(explode(',',$tagz));
        // BLOG CATEGORIES
        $bcats = BlogCategory::where('language_id',$this->language->id)->get();
        // BLOGS
        $blogs = Blog::where('language_id',$this->language->id)->latest()->paginate($this->gs->post_count);
            if($request->ajax()){
                return view('front.ajax.blog',compact('blogs'));
            }
		return view('frontend.blog',compact('blogs','bcats','tags'));
	}

    public function blogcategory(Request $request, $slug)
    {

        // BLOG TAGS
        $tags = null;
        $tagz = '';
        $name = Blog::where('language_id',$this->language->id)->pluck('tags')->toArray();
        foreach($name as $nm)
        {
            $tagz .= $nm.',';
        }
        $tags = array_unique(explode(',',$tagz));
        // BLOG CATEGORIES
        $bcats = BlogCategory::where('language_id',$this->language->id)->get();
        // BLOGS
        $bcat = BlogCategory::where('language_id',$this->language->id)->where('slug', '=', str_replace(' ', '-', $slug))->first();
        $blogs = $bcat->blogs()->where('language_id',$this->language->id)->latest()->paginate($this->gs->post_count);
            if($request->ajax()){
                return view('front.ajax.blog',compact('blogs'));
            }
        return view('frontend.blog',compact('bcat','blogs','bcats','tags'));
    }

    public function blogtags(Request $request, $slug)
    {

        // BLOG TAGS
        $tags = null;
        $tagz = '';
        $name = Blog::where('language_id',$this->language->id)->pluck('tags')->toArray();
        foreach($name as $nm)
        {
            $tagz .= $nm.',';
        }
        $tags = array_unique(explode(',',$tagz));
        // BLOG CATEGORIES
        $bcats = BlogCategory::where('language_id',$this->language->id)->get();
        // BLOGS
        $blogs = Blog::where('language_id',$this->language->id)->where('tags', 'like', '%' . $slug . '%')->paginate($this->gs->post_count);
            if($request->ajax()){
                return view('front.ajax.blog',compact('blogs'));
            }
        return view('frontend.blog',compact('blogs','slug','bcats','tags'));
    }

    public function blogsearch(Request $request)
    {


        $tags = null;
        $tagz = '';
        $name = Blog::where('language_id',$this->language->id)->pluck('tags')->toArray();
        foreach($name as $nm)
        {
            $tagz .= $nm.',';
        }
        $tags = array_unique(explode(',',$tagz));
        // BLOG CATEGORIES
        $bcats = BlogCategory::where('language_id',$this->language->id)->get();
        // BLOGS
        $search = $request->search;
        $blogs = Blog::where('language_id',$this->language->id)->where('title', 'like', '%' . $search . '%')->orWhere('details', 'like', '%' . $search . '%')->paginate($this->gs->post_count);
            if($request->ajax()){
                return view('frontend.ajax.blog',compact('blogs'));
            }
        return view('frontend.blog',compact('blogs','search','bcats','tags'));
    }

    public function blogshow($slug)
    {


        // BLOG TAGS
        $tags = null;
        $tagz = '';
        $name = Blog::where('language_id',$this->language->id)->pluck('tags')->toArray();
        foreach($name as $nm)
        {
            $tagz .= $nm.',';
        }
        $tags = array_unique(explode(',',$tagz));
        // BLOG CATEGORIES
        $bcats = BlogCategory::where('language_id',$this->language->id)->get();
        // BLOGS

        $blog = Blog::where('slug',$slug)->first();

        $blog->views = $blog->views + 1;
        $blog->update();
        // BLOG META TAG
        $blog_meta_tag = $blog->meta_tag;
        $blog_meta_description = $blog->meta_description;
        return view('frontend.blogshow',compact('blog','bcats','tags','blog_meta_tag','blog_meta_description'));
    }

    // -------------------------------- BLOG SECTION ENDS----------------------------------------

    // -------------------------------- FAQ SECTION ----------------------------------------
        public function faq()
        {
            if(DB::table('pagesettings')->first()->faq == 0){
                return redirect()->back();
            }
            $faqs =  DB::table('faqs')->where('language_id',$this->language->id)->latest('id')->get();
            $count = count(DB::table('faqs')->where('language_id',$this->language->id)->get()) / 2;
            if(($count % 1) != 0){
                $chunk = (int)$count + 1;
            }
            else{
                $chunk = $count;
            }
            return view('frontend.faq',compact('faqs','chunk'));
        }
    // -------------------------------- FAQ SECTION ENDS----------------------------------------


    // -------------------------------- AUTOSEARCH SECTION ----------------------------------------

    public function autosearch($slug)
    {
        if(mb_strlen($slug,'UTF-8') > 1){
            $search = ' '.$slug;
            $prods = Product::where('name', 'like', '%' . $search . '%')->orWhere('name', 'like', $slug . '%')->where('status','=',1)->orderby('id','desc')->take(10)->get();
            return view('load.suggest',compact('prods','slug'));
        }
        return "";
    }

    // -------------------------------- AUTOSEARCH SECTION ENDS ----------------------------------------


    // -------------------------------- CONTACT SECTION ----------------------------------------

	public function contact()
	{

        if(DB::table('pagesettings')->first()->contact == 0){
            return redirect()->back();
        }
        $ps = $this->ps;
		return view('frontend.contact',compact('ps'));
	}


    //Send email to admin
    public function contactemail(Request $request)
    {
        $gs = $this->gs;

        if($gs->is_capcha == 1)
        {
            $rules = [
                'g-recaptcha-response' => 'required'
            ];
            $customs = [
                'g-recaptcha-response.required' => "Please verify that you are not a robot.",
            ];

            $validator = Validator::make($request->all(), $rules, $customs);
            if ($validator->fails()) {
              return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }
        }


        // Logic Section
        $subject = "Email From Of ".$request->name;
        $to = $request->to;
        $name = $request->name;
        $phone = $request->phone;
        $from = $request->email;
        $msg = "Name: ".$name."\nEmail: ".$from."\nPhone: ".$phone."\nMessage: ".$request->text;
        if($gs->is_smtp)
        {
        $data = [
            'to' => $to,
            'subject' => $subject,
            'body' => $msg,
        ];

        $mailer = new GeniusMailer();
        $mailer->sendCustomMail($data);
        }
        else
        {
        $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
        mail($to,$subject,$msg,$headers);
        }
        // Logic Section Ends

        // Redirect Section
        return response()->json(__('Success! Thanks for contacting us, we will get back to you shortly.'));
    }

    // Refresh Capcha Code
    public function refresh_code(){
        $this->code_image();
        return "done";
    }

    // -------------------------------- CONTACT SECTION ENDS ----------------------------------------


    // -------------------------------- SUBSCRIBE SECTION ----------------------------------------

    public function subscribe(Request $request)
    {
        $subs = Subscriber::where('email','=',$request->email)->first();
        if(isset($subs)){
        return response()->json(array('errors' => [ 0 => __('This Email Has Already Been Taken.')]));
        }
        $subscribe = new Subscriber;
        $subscribe->fill($request->all());
        $subscribe->save();
        return response()->json(__('You Have Subscribed Successfully.'));
    }

    // -------------------------------- SUBSCRIBE SECTION  ENDS----------------------------------------

    // -------------------------------- MAINTENANCE SECTION ----------------------------------------

    public function maintenance()
    {
        $gs = $this->gs;
            if($gs->is_maintain != 1) {
                return redirect()->route('front.index');
            }

        return view('frontend.maintenance');
    }

    // -------------------------------- MAINTENANCE SECTION ----------------------------------------


    // -------------------------------- VENDOR SUBSCRIPTION CHECK SECTION ----------------------------------------





}
