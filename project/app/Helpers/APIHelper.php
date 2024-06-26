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
                'images' => [
                    asset('images/' . $product->photo),
                    asset('images/' . $product->thumbnail)
                ],
                'createdDate' => Carbon::parse($product->created_at)->format('Y-m-d H:i:s'),
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
                'image' => asset('images/' . $slider->photo),
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
                'logo' => asset('images/' . $brand->logo),
            ];
        });
    }
}
