<?php

namespace App\Http\Controllers\Payment\Checkout;

use App\Http\Controllers\Controller;
use DB;
use App;
use Session;

class CheckoutBaseControlller extends Controller
{
    protected $gs;
    protected $ps;
    protected $curr;

    public function __construct()
    {

        $this->gs = DB::table('generalsettings')->find(1);

        $this->ps = DB::table('pagesettings')->find(1);

        $this->middleware(function ($request, $next) {

            if (Session::has('language')) 
            {
                $this->language = DB::table('languages')->find(Session::get('language'));
            }
            else
            {
                $this->language = DB::table('languages')->where('is_default','=',1)->first();
            }  

            App::setlocale($this->language->name);
            view()->share('langg', $this->language);
            if (Session::has('currency')) {
                $this->curr = DB::table('currencies')->find(Session::get('currency'));
            }
            else {
                $this->curr = DB::table('currencies')->where('is_default','=',1)->first();
            }
    
            return $next($request);
        });
    }
}