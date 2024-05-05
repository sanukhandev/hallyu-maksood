<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = ['user_id','product_id','review','rating','review_date'];
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo('App\Models\Product')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withDefault();
    }

    public static function normalRating($productid){
        $stars = Rating::where('product_id',$productid)->avg('rating');
        return number_format($stars,1);
    }

    public static function ratings($productid){
        $stars = Rating::where('product_id',$productid)->avg('rating');
        $ratings = number_format((float)$stars, 1, '.', '') * 20;
        return $ratings;
    }

    public static function ratingCount($productid){
        $stars = Rating::where('product_id',$productid)->count();
        return number_format($stars);
    }

    public static function customRatings($productid,$rating){
        $totalCount = Rating::where('product_id',$productid)->count();
        if($totalCount == 0){
            return 0;
        }
        $ratingCount = Rating::where('product_id',$productid)->where('rating',$rating)->count();
        $avg = ($ratingCount / $totalCount) * 100;
        return $avg ;
    }

    public static function customRatingsCount($productid,$rating){
        $totalCount = Rating::where('product_id',$productid)->count();
        if($totalCount == 0){
            return 0;
        }
        $ratingCount = Rating::where('product_id',$productid)->where('rating',$rating)->count();
        $avg = ($ratingCount / $totalCount) * 100;
        return round($avg,2).'%';
    }

    public static function vendorRatings($user_id){

        $stars = Rating::whereHas('product', function($query) use ($user_id) {
            $query->where('user_id', '=', $user_id);
         })->avg('rating');
         $ratings = number_format((float)$stars, 1, '.', '') * 20;
         return $ratings;
    }

    public static function vendorRatingCount($user_id){

        $stars = Rating::whereHas('product', function($query) use ($user_id) {
            $query->where('user_id', '=', $user_id);
         })->count();

         return $stars;
    }

}
