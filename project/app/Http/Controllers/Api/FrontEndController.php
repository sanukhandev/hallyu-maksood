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
        $this->locale = 1;

    }

    public function getLocaleCode($headerCode)
    {
        if ($headerCode == '0' || $headerCode == 0) {
            return 1;
        } elseif ($headerCode == '1' || $headerCode == 1) {
            return 4;
        } else {
            return 1;
        }
    }

    public function index(Request $request)
    {
        $this->locale = $this->getLocaleCode($request->header('Language'));
        $data['sliders'] = $this->apiHelper->mapSlider(DB::table('sliders')->where('language_id', $this->locale)->get());
        $data['brands'] = $this->apiHelper->mapBrands(DB::table('brands')->where('brand_is_active', 1)->get());
        $data['categories'] = $this->apiHelper->mapCategories(Category::where('status', 1)->where('language_id', $this->locale)->get());
        $productsQuery = Product::with(['ratings', 'brand', 'category'])->where('language_id', $this->locale)->get();
        $data['products'] = $this->apiHelper->mapProducts($productsQuery);
        $data['featured'] = $this->apiHelper->mapProducts($productsQuery->where('featured', 1));
        $data['best'] = $this->apiHelper->mapProducts($productsQuery->where('best', 1));
        $data['top'] = $this->apiHelper->mapProducts($productsQuery->where('top', 1));
        $data['hot'] = $this->apiHelper->mapProducts($productsQuery->where('hot', 1));
        $data['latest'] = $this->apiHelper->mapProducts($productsQuery->where('latest', 1));
        $data['big'] = $this->apiHelper->mapProducts($productsQuery->where('big', 1));
        $data['trending'] = $this->apiHelper->mapProducts($productsQuery->where('trending', 1));
        $data['sale'] = $this->apiHelper->mapProducts($productsQuery->where('sale', 1));

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
        $this->locale = $this->getLocaleCode($request->header('Language'));
        $category = $request->category;
        $brand = $request->brand;
        $minPrice = $request->minPrice ?? 0;
        $maxPrice = $request->maxPrice ?? 999999;
        $sort = $request->sort;
        $search = $request->search;
        $limit = $request->limit ?? 10;

        // Sorting options
        $sortingOptions = [
            'price_asc' => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'newest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
        ];

        // Query products with applied filters and sorting
        $productsQuery = Product::with(['ratings', 'brand', 'category'])
            ->where('language_id', $this->locale)
            ->when($category, function ($query) use ($category) {
                $query->where(function ($query) use ($category) {
                    $query->where('category_id', $category)
                        ->orWhere('subcategory_id', $category)
                        ->orWhere('childcategory_id', $category);
                });
            })
            ->when($brand, function ($query) use ($brand) {
                return $query->where('brand_id', $brand);
            })
            ->when($minPrice || $maxPrice, function ($query) use ($minPrice, $maxPrice) {
                return $query->whereBetween('price', [$minPrice, $maxPrice]);
            })
            ->when($sort, function ($query) use ($sort, $sortingOptions) {
                if (isset($sortingOptions[$sort])) {
                    return $query->orderBy($sortingOptions[$sort][0], $sortingOptions[$sort][1]);
                }
            })
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            });

        // Paginate and fetch the results
        $products = $productsQuery->paginate($limit);

        // Use the helper to map the products
        $this->apiHelper = new APIHelper();
        $data['products'] = $this->apiHelper->mapProducts($products);

        // Map the pagination data
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
