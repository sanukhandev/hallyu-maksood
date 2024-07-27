<?php

namespace App\Helpers;

use Carbon\Carbon;


class APIHelper
{

    public function mapProducts($products)
    {
        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->name,
                'brandName' => optional($product->brand)->brand_name ?? null, // Handle null brand
                'thumbnail' => asset('assets/images/thumbnails/' . $product->thumbnail),
                'gallery' => array_merge(
                    [asset('assets/images/products/' . $product->photo)],
                    $product->galleries->map(function ($gallery) {
                        return asset('assets/images/galleries/' . $gallery->photo);
                    })->toArray()
                ),
                'stock' => $product->stock ?? -1,
                'description' => $product->details,
                'createdDate' => Carbon::parse($product->created_at)->format('F j, Y g:i:s A'),
                'salePercent' => $product->previous_price > 0 ? round((($product->previous_price - $product->price) / $product->previous_price) * 100) : 0,
                'salePrice' => $product->price,
                'previousPrice' => $product->previous_price,
                'numberReviews' => $product->ratings->count(),
                'reviewStars' => $product->ratings->count() ? round($product->ratings->avg('rating'), 1) : 0,
                'categoryName' => optional($product->category)->name ?? null, // Handle null category
                'subCategoryName' => optional($product->subcategory)->name ?? null, // Handle null subcategory
                'childCategoryName' => optional($product->childcategory)->name ?? null, // Handle null child category
                'colors' => $this->mapColorsAndSizes($product)
            ];
        });
    }

    private function mapColorsAndSizes($product)
    {
        $colors = [];
        if ($product->color_all && $product->size_all) {
            $colorList = explode(',', $product->color_all);
            $sizeList = explode(',', $product->size_all);
            $sizesQty = explode(',', $product->size_qty);
            $sizesPrice = explode(',', $product->size_price);

            foreach ($colorList as $color) {
                $sizes = [];
                foreach ($sizeList as $index => $size) {
                    $sizes[] = [
                        'size' => $size,
//                        'price' => $sizesPrice[$index],
//                        'quantity' => $sizesQty[$index]
                    ];
                }
                $colors[] = [
                    'color' => $color,
                    'sizes' => $sizes
                ];
            }
        }

        return $colors;
    }

    public function mapSlider($sliders)
    {
        return $sliders->map(function ($slider) {
            return [
                'id' => $slider->id,
                'title' => $slider->subtitle_text,
                'description' => $slider->details_text,
                'image' => asset( 'assets/images/sliders/' . $slider->photo),
                'link' => $slider->link,
            ];
        });
    }

    public function mapBrands($brands)
    {
        return $brands->map(function ($brand) {
            return [
                'id' => $brand->brand_id,
                'name' => $brand->brand_name,
                'country' => $brand->brand_country,
                'logo' => asset($brand->brand_logo),
            ];
        });
    }

   public function mapCategories($categories)
        {
            return $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,

                    'subs' => $category->subs->map(function ($sub) {
                        return [
                            'id' => $sub->id,
                            'name' => $sub->name,
                            'image' => asset($sub->photo),
                            'childs' => $sub->childs->map(function ($child) {
                                return [
                                    'id' => $child->id,
                                    'name' => $child->name,
                                ];
                            })
                        ];
                    })
                ];
            });
        }

    public function mapProduct($product){
        if ($product){return [
            'id' => $product->id,
            'title' => $product->name,
            'brandName' => optional($product->brand)->brand_name ?? null, // Handle null brand
            'thumbnail' => asset('assets/images/thumbnails/' . $product->thumbnail),
            'gallery' => array_merge(
                [asset('assets/images/products/' . $product->photo)],
                $product->galleries->map(function ($gallery) {
                    return asset('assets/images/galleries/' . $gallery->photo);
                })->toArray()
            ),
            'stock' => $product->stock ?? -1,
            'category_id' => $product->category_id,
            'description' => $product->details,
            'createdDate' => Carbon::parse($product->created_at)->format('F j, Y g:i:s A'),
            'salePercent' => $product->previous_price > 0 ? round((($product->previous_price - $product->price) / $product->previous_price) * 100) : 0,
            'salePrice' => $product->price,
            'previousPrice' => $product->previous_price,
            'numberReviews' => $product->ratings->count(),
            'reviewStars' => $product->ratings->count() ? round($product->ratings->avg('rating'), 1) : 0,
            'categoryName' => optional($product->category)->name ?? null, // Handle null category
            'subCategoryName' => optional($product->subcategory)->name ?? null, // Handle null subcategory
            'childCategoryName' => optional($product->childcategory)->name ?? null, // Handle null child category
            'colors' => $this->mapColorsAndSizes($product)
        ];}
        return null;
    }

    public function mapCart($cart){


        return $cart->map(function ($cartItem){
            return [
                'id' => $cartItem->id,
                'user_id' => $cartItem->user_id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'total_price' => $cartItem->total_price,
                'created_at' => $cartItem->created_at,
                'updated_at' => $cartItem->updated_at,
                'product' => $this->mapProduct($cartItem->product)
            ];
        });
    }

    public static function  generateOrderNumber(): string
    {
        return 'ORD-' . date('Y') . '-' . strtoupper(uniqid());
    }
    public function transformData($data) {
        $result = [
            "totalQty" => 0,
            "totalPrice" => 0,
            "items" => []
        ];

        foreach ($data as $order) {
            $product = $order['product'];
            $productId = $product['id'];

            $result['items'][$productId] = [
                "qty" => $order['quantity'],
                "size_key" => 0,
                "size_qty" => $product['size_qty'] ?? "",
                "size_price" => $product['size_price'] ?? "",
                "size" => $product['size'] ?? "",
                "color" => $product['color'] ?? "",
                "stock" => $product['stock'] ?? null,
                "price" => (float) $order['total_price'],
                "item" => [
                    "id" => $product['id'],
                    "user_id" => 0,
                    "slug" => $product['slug'],
                    "name" => $product['name'],
                    "photo" => $product['photo'],
                    "size" => $product['size'] ?? "",
                    "size_qty" => $product['size_qty'] ?? "",
                    "size_price" => $product['size_price'] ?? "",
                    "color" => $product['color'] ?? "",
                    "price" => $product['price'],
                    "stock" => $product['stock'] ?? null,
                    "type" => $product['type'],
                    "file" => $product['file'] ?? null,
                    "link" => $product['link'] ?? null,
                    "license" => $product['license'] ?? "",
                    "license_qty" => $product['license_qty'] ?? "",
                    "measure" => $product['measure'] ?? null,
                    "whole_sell_qty" => $product['whole_sell_qty'] ?? "",
                    "whole_sell_discount" => $product['whole_sell_discount'] ?? "",
                    "attributes" => $product['attributes'] ?? null,
                    "size_all" => $product['size_all'] ?? null,
                    "color_all" => $product['color_all'] ?? null,
                    "minimum_qty" => $product['minimum_qty'] ?? null,
                    "stock_check" => $product['stock_check'] ?? 0
                ],
                "license" => $product['license'] ?? "",
                "dp" => "0",
                "keys" => "",
                "values" => "",
                "item_price" => (float) $order['total_price'],
                "discount" => 0,
                "affilate_user" => 0
            ];

            $result['totalQty'] += $order['quantity'];
            $result['totalPrice'] += (float) $order['total_price'];
        }

        return $result;
    }

}
