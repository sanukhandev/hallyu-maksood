<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use http\Client\Curl\User;
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
    public function __construct()
    {
        $this->apiHelper = new APIHelper();
    }

    public function index()
    {

        $data['sliders'] = $this->apiHelper->mapSlider(DB::table('sliders')->where('language_id', 1)->get());
        $data['brands'] = $this->apiHelper->mapbrands(DB::table('brands')->where('brand_is_active', 1)->get());
        $data['categories'] = $this->apiHelper->mapCategories(Category::where('status', 1)->get());
        $products = Product::with(['ratings', 'brand', 'category']);
        $data['products'] = $this->apiHelper->mapProducts($products->get());
        $data['featured'] = $this->apiHelper->mapProducts($products->where('featured', 1)->get());
        $data['best'] = $this->apiHelper->mapProducts($products->where('best', 1)->get());
        $data['top'] = $this->apiHelper->mapProducts($products->where('top', 1)->get());
        $data['hot'] = $this->apiHelper->mapProducts($products->where('hot', 1)->get());
        $data['latest'] = $this->apiHelper->mapProducts($products->where('latest', 1)->get());
        $data['big'] = $this->apiHelper->mapProducts($products->where('big', 1)->get());
        $data['trending'] = $this->apiHelper->mapProducts($products->where('trending', 1)->get());
        $data['sale'] = $this->apiHelper->mapProducts($products->where('sale', 1)->get());
        $data['ratings'] = Rating::all();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function showProduct($id)
    {
        $data['product'] = $this->apiHelper->mapProduct(Product::with(['ratings', 'brand', 'category'])->find($id));
        if (!$data['product']) return response()->json([
            'status' => 404,
            'message' => 'Product not found'
        ]);
        $data['related'] = $this->apiHelper->mapProducts(Product::with(['ratings', 'brand', 'category'])->where('category_id', $data['product']['category_id'])
            ->whereNotIn('id', [$id])
            ->take(10)->get());

        return response()->json([
            'status' => 200,
            'data' => $data ?? false
        ]);
    }


    public function getProducts(Request $request)
    {
        $category = $request->category;
        $brand = $request->brand;
        $minPrice = $request->minPrice ?? 0;
        $maxPrice = $request->maxPrice ?? 999999;
        $sort = $request->sort;
        $search = $request->search;
        $limit = $request->limit ?? 10;
        $sortingOptions = [
            'price_asc' => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'newest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
        ];

        // Query products with applied filters and sorting
        $products = Product::with(['ratings', 'brand', 'category'])
            ->when($category, function ($query, $category) {
                return $query->where('category_id', $category)
                    ->orWhere('subcategory_id', $category)
                    ->orWhere('childcategory_id', $category);
            })
            ->when($brand, function ($query, $brand) {
                return $query->where('brand_id', $brand);
            })
            ->when($minPrice || $maxPrice, function ($query) use ($minPrice, $maxPrice) {
                return $query->whereBetween('price', [$minPrice, $maxPrice]);
            })
            ->when($sort, function ($query, $sort) use ($sortingOptions) {
                if (isset($sortingOptions[$sort])) {
                    return $query->orderBy($sortingOptions[$sort][0], $sortingOptions[$sort][1]);
                }
                return $query;
            })
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate($limit);

        // Map the paginated results
        $this->apiHelper = new APIHelper();
        $data['products'] = $this->apiHelper->mapProducts($products);
        $data['pagination'] = [
            'current_page' => $products->currentPage(),
            'first_page_url' => $products->url(1),
            'from' => $products->firstItem(),
            'last_page' => $products->lastPage(),
            'last_page_url' => $products->url($products->lastPage()),
            'next_page_url' => $products->nextPageUrl(),
            'path' => $products->url(1),
            'per_page' => $products->perPage(),
            'prev_page_url' => $products->previousPageUrl(),
            'to' => $products->lastItem(),
            'total' => $products->total()
        ];

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function cart()
    {
        $data['cart'] = [];
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }


    public function get_product_reviews($id)
    {
        $data['reviews'] = Rating::where('product_id', $id)->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }


}
