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
        // Eager load the necessary relationships and limit the number of queries
        $products = Product::with(['ratings'])
            ->get()
            ->groupBy(function($item) {
                return [
                    'featured' => $item->featured,
                    'best' => $item->best,
                    'top' => $item->top,
                    'hot' => $item->hot,
                    'latest' => $item->latest,
                    'big' => $item->big,
                    'trending' => $item->trending,
                    'sale' => $item->sale
                ];
            });

        $data['sliders'] = DB::table('sliders')->where('language_id', 1)->get();
        $data['arrivals'] = ArrivalSection::where('status', 1)->get();
        $data['products'] = $products->take(10);
        $data['featured'] = $products->get(1)->get('featured');
        $data['best'] = $products->get(1)->get('best');
        $data['top'] = $products->get(1)->get('top');
        $data['hot'] = $products->get(1)->get('hot');
        $data['latest'] = $products->get(1)->get('latest');
        $data['big'] = $products->get(1)->get('big');
        $data['trending'] = $products->get(1)->get('trending');
        $data['sale'] = $products->get(1)->get('sale');
        $data['ratings'] = $products->pluck('ratings')->flatten();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function showProduct($id)
    {
        $data['product'] = Product::with('brand')->where('id', $id)->first();
        $data['ratings'] = Rating::where('product_id', $id)->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function searchProduct(Request $request)
    {
        $data['products'] = Product::where('name', 'like', '%' . $request->name . '%')->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function searchProductByCategory(Request $request)
    {
        $data['products'] = Product::where('category_id', $request->category_id)->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function searchProductByBrand(Request $request)
    {
        $data['products'] = Product::where('brand_id', $request->brand_id)->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function searchProductByPrice(Request $request)
    {
        $data['products'] = Product::whereBetween('price', [$request->min_price, $request->max_price])->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function searchProductByRating(Request $request)
    {
        $data['products'] = Product::where('rating', $request->rating)->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }


    public function searchProductByPriceAndRating(Request $request)
    {
        $data['products'] = Product::whereBetween('price', [$request->min_price, $request->max_price])->where('rating', $request->rating)->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }


    // show all products paginated

    public function showAllProducts($pageNumber=10)
    {
        $data['products'] = Product::paginate($pageNumber);
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }


}
