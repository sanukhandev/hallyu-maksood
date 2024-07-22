<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Coupon, Order, Package, Product, Rating, Shipping, UserCartItems};
use Illuminate\Http\Request;
use App\Helpers\APIHelper;

class CartController extends Controller
{
    protected $userCartItems;
    protected $userId;

    public function __construct()
    {
        $this->apiHelper = new APIHelper();
        $this->userCartItems = new UserCartItems();
        $this->userId = null;
    }

    public function addToCart(Request $request)
    {
        $this->userId = $request->user()->id;
        $product_id = $request->product_id;
        $quantity = $request->quantity;
        $product = Product::find($product_id);
        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ]);
        }
        $total_price = $product->price * $quantity;
        $existingCartItem = $this->userCartItems->getCartItem($this->userId, $product_id);
        if ($existingCartItem) {
            $existingCartItem->quantity += $quantity;
            $existingCartItem->total_price += $total_price;
            $this->userCartItems->updateCartItem($this->userId, $product_id, $existingCartItem->quantity, $existingCartItem->total_price);
        } else {
            $this->userCartItems->addCartItem($this->userId, $product_id, $quantity, $total_price);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Product added to cart successfully'
        ]);
    }


    public function updateCart(Request $request)
    {
        $this->userId = $request->user()->id;
        $product_id = $request->product_id;
        $quantity = $request->quantity;
        $product = Product::find($product_id);
        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ]);
        }
        $total_price = $product->price * $quantity;
        $this->userCartItems->updateCartItem($this->userId, $product_id, $quantity, $total_price);
        return response()->json([
            'status' => 200,
            'message' => 'Cart updated successfully'
        ]);
    }

    public function deleteCart(Request $request)
    {
        $this->userId = $request->user()->id;
        $product_id = $request->product_id;
        $this->userCartItems->deleteCartItem($this->userId, $product_id);
        return response()->json([
            'status' => 200,
            'message' => 'Product removed from cart successfully'
        ]);
    }

    public function deleteAllCart(Request $request)
    {
        $this->userId = $request->user()->id;
        $this->userCartItems->deleteAllCartItems($this->userId);
        return response()->json([
            'status' => 200,
            'message' => 'Cart cleared successfully'
        ]);
    }

    public function getCart(Request $request)
    {
        $this->userId = $request->user()->id;
        $cart = $this->apiHelper->mapCart($this->userCartItems->getCartItems($this->userId));
        return response()->json([
            'status' => 200,
            'data' => $cart
        ]);
    }


    public function checkout_cod(Request $request)
    {
        $this->userId = $request->user()->id;
        $input = $request->all();
        $total = $this->userCartItems->getCartTotal($this->userId);
        $count = $this->userCartItems->getCartCount($this->userId);
        $quantity = $this->userCartItems->getCartQuantity($this->userId);
        $cart = $this->userCartItems->getCartItems($this->userId);
        $order = new Order;
        $order->fill($input);
        $order->user_id = $this->userId;
        $order->cart = json_encode($cart);
        $order->total = $total;
        $order->order_number = Str::random(4) . time();
        $order->save();

        $this->userCartItems->deleteAllCartItems($this->userId);
        return response()->json([
            'status' => 200,
            'message' => 'COD Checkout successful',
            'total' => $total,
            'count' => $count,
            'quantity' => $quantity
        ]);

    }

    public function add_review_by_product_id(Request $request)
    {
        $this->userId = $request->user()->id;
        $input = $request->all();
        $product_id = $request->product_id;
        $product = Product::find($product_id);
        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ]);
        }
        $rating = new Rating;
        $rating->fill($input);
        $rating->user_id = $this->userId;
        $rating->product_id = $product_id;
        $rating->review_date = date('Y-m-d H:i:s');
        $rating->save();
        return response()->json([
            'status' => 200,
            'message' => 'Review added successfully'
        ]);

    }

    public function get_order_options()
    {
        return response()->json(['status' => 200, 'data' => ['shipping' => Shipping::get(), 'packages' => Package::get()]]);
    }

    public function apply_coupon(Request $request)
    {
        $this->userId = $request->user()->id;
        $couponCode = $request->coupon;
        $cart = $this->userCartItems->getCartItems($this->userId);
        $grandTotal = $this->userCartItems->getCartTotal($this->userId);

        $total = 0;
        $coupon = Coupon::where('code', $couponCode)->first();

        // Check if coupon is valid for category
        if ($coupon && $coupon->category) {
            $validCategory = false;

            foreach ($cart as $item) {
                if ($item->product->category_id == $coupon->category) {
                    $total += $item->total_price;
                    if (!$validCategory) {
                        $validCategory = true;
                    }
                }
            }

            if (!$validCategory) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Coupon is not valid for this category'
                ]);
            }
        } else {
            $total = $grandTotal;
        }

        $discount = 0;
        $couponType = 'None';

        if ($coupon) {
            $discount = $this->calculateDiscount($coupon, $total);
            $couponType = $coupon->type == 0 ? 'Percentage' : 'Fixed';
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'oldTotal' => $grandTotal,
                'newTotal' => $grandTotal - $discount,
                'discount' => $discount,
                'code' => $couponCode,
                'value' => $couponType
            ]
        ]);
    }

    public function calculateDiscount($coupon, $total)
    {
        $currentDate = date('Y-m-d');

        if ($coupon->start_date <= $currentDate && $coupon->end_date >= $currentDate) {
            if ($coupon->type == 0) {
                $discount = $total * ($coupon->price / 100);
            } else {
                $discount = min($coupon->price, $total); // Ensure discount does not exceed total
            }
        } else {
            $discount = 0;
        }

        return $discount;
    }


}
