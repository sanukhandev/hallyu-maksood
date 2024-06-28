<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\APIHelper;

use App\Models\ArrivalSection;
use App\Models\{
    Product,
    Category,
    Rating
};

use DB;


class FrontEndController extends Controller
{
    public function index()
    {
        $apiHelper = new APIHelper();
        $data['sliders'] = $apiHelper->mapSlider(DB::table('sliders')->where('language_id', 1)->get());
        $data['brands'] = $apiHelper->mapbrands(DB::table('brands')->where('brand_is_active', 1)->get());
        $data['categories'] = $apiHelper->mapCategories(Category::where('status', 1)->get());
        $products = Product::with(['ratings', 'brand', 'category']);
        $data['products'] = $apiHelper->mapProducts($products->get());
        $data['featured'] = $apiHelper->mapProducts($products->where('featured', 1)->get());
        $data['best'] = $apiHelper->mapProducts($products->where('best', 1)->get());
        $data['top'] = $apiHelper->mapProducts($products->where('top', 1)->get());
        $data['hot'] = $apiHelper->mapProducts($products->where('hot', 1)->get());
        $data['latest'] = $apiHelper->mapProducts($products->where('latest', 1)->get());
        $data['big'] = $apiHelper->mapProducts($products->where('big', 1)->get());
        $data['trending'] = $apiHelper->mapProducts($products->where('trending', 1)->get());
        $data['sale'] = $apiHelper->mapProducts($products->where('sale', 1)->get());
        $data['ratings'] = Rating::all();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function showProduct($id)
    {
        $data['product'] = Product::with(['ratings','brand','category','subcategory','childcategory','galleries','comments'])->where('id', $id)->first();
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

    public function searchProductByCategory($id)
    {
        $data['products'] = Product::where('category_id', $id)->get();
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
