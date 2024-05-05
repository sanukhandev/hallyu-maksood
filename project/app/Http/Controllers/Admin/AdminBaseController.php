<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use App;

class AdminBaseController extends Controller
{
    protected $gs;
    protected $curr;
    protected $language_id;
    public function __construct()
    {
        $this->middleware('auth:admin');

        // Set Global GeneralSettings

        $this->gs = DB::table('generalsettings')->find(1);

        // Set Global Language

        $this->language = DB::table('admin_languages')->where('is_default','=',1)->first();
        view()->share('langg', $this->language);
        App::setlocale($this->language->name);
    
        // Set Global Currency

        $this->curr = DB::table('currencies')->where('is_default','=',1)->first();

    }
}
