<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserCartItems;
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
        $this->userId = 49; // Assuming 49 is the current user's ID for demonstration. Replace with actual user ID logic.
    }

    public function addToCart(Request $request)
    {
        $product_id = $request->product_id;
        $quantity = $request->quantity;
        $total_price = $request->total_price;
        $this->userCartItems->addCartItem($this->userId, $product_id, $quantity, $total_price);
        return response()->json([
            'status' => 200,
            'message' => 'Product added to cart successfully'
        ]);
    }

    public function updateCart(Request $request)
    {
        $product_id = $request->product_id;
        $quantity = $request->quantity;
        $total_price = $request->total_price;
        $this->userCartItems->updateCartItem($this->userId, $product_id, $quantity, $total_price);
        return response()->json([
            'status' => 200,
            'message' => 'Cart updated successfully'
        ]);
    }

    public function deleteCart(Request $request)
    {
        $product_id = $request->product_id;
        $this->userCartItems->deleteCartItem($this->userId, $product_id);
        return response()->json([
            'status' => 200,
            'message' => 'Product removed from cart successfully'
        ]);
    }

    public function deleteAllCart()
    {
        $this->userCartItems->deleteAllCartItems($this->userId);
        return response()->json([
            'status' => 200,
            'message' => 'Cart cleared successfully'
        ]);
    }

    public function getCart()
    {
        $cart = $this->apiHelper->mapCart($this->userCartItems->getCartItems($this->userId));
        return response()->json([
            'status' => 200,
            'data' => $cart
        ]);
    }
}
