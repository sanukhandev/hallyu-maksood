<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ArrivalSection;
use App\Models\Product;
use App\Models\Rating;
use DB;


class FrontEndController extends Controller
{
    public function index()
    {
        $data['sliders'] = DB::table('sliders')
            ->where('language_id', 1)
            ->get();



        $data['arrivals']=ArrivalSection::where('status',1)->get();
        $data['products']=Product::get();
        $data['ratings']=Rating::get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
}
