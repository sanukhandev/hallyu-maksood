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
        $data['product'] = Product::with(['ratings', 'brand', 'category', 'subcategory', 'childcategory', 'galleries', 'comments'])->where('id', $id)->first();
        $data['ratings'] = Rating::where('product_id', $id)->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }


    public function getProducts(Request $request)
    {
        // get url parameters with default values
        $category = $request->category;
        $brand = $request->brand;
        $minPrice = $request->minPrice ?? 0;
        $maxPrice = $request->maxPrice ?? 999999;
        $sort = $request->sort;
        $search = $request->search;
        $limit = $request->limit ?? 10;

        // get products with filters
        $products = Product::with(['ratings', 'brand', 'category'])
            ->when($category, function ($query, $category) {
                return $query->where('category_id', $category)->orWhere('subcategory_id', $category)->orWhere('childcategory_id', $category);
            })
            ->when($brand, function ($query, $brand) {
                return $query->where('brand_id', $brand);
            })
            ->when($minPrice || $maxPrice, function ($query) use ($minPrice, $maxPrice) {
                return $query->whereBetween('price', [$minPrice, $maxPrice]);
            })
            ->when($sort, function ($query, $sort) {
                return $query->orderBy('price', $sort);
            })
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            });

        // paginate and map response
        $res = $products->paginate($limit);

        $apiHelper = new APIHelper();
        $data['products'] = $apiHelper->mapProducts($res);
        $data['pagination'] = [
            'current_page' => $res->currentPage(),
            'first_page_url' => $res->url(1),
            'from' => $res->firstItem(),
            'last_page' => $res->lastPage(),
            'last_page_url' => $res->url($res->lastPage()),
            'next_page_url' => $res->nextPageUrl(),
            'path' => $res->url(1),
            'per_page' => $res->perPage(),
            'prev_page_url' => $res->previousPageUrl(),
            'to' => $res->lastItem(),
            'total' => $res->total()
        ];

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }


}
