<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCartItems extends Model
{
    protected $fillable = ['user_id', 'product_id', 'quantity', 'total_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCartItems($user_id)
    {
        return self::where('user_id', $user_id)->with('product')->get();
    }

    public function addCartItem($user_id, $product_id, $quantity, $total_price)
    {
        return self::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'total_price' => $total_price
        ]);
    }

    public function updateCartItem($user_id, $product_id, $quantity, $total_price)
    {
        return self::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->update([
                'quantity' => $quantity,
                'total_price' => $total_price
            ]);
    }

    public function deleteCartItem($user_id, $product_id)
    {
        return self::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->delete();
    }

    public function deleteAllCartItems($user_id)
    {
        return self::where('user_id', $user_id)->delete();
    }

    public function getCartTotal($user_id)
    {
        return self::where('user_id', $user_id)->sum('total_price');
    }

    public function getCartCount($user_id)
    {
        return self::where('user_id', $user_id)->count();
    }

    public function getCartQuantity($user_id)
    {
        return self::where('user_id', $user_id)->sum('quantity');
    }

    public function getCartItemsCount($user_id, $product_id)
    {
        return self::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->count();
    }

    public function getCartItem($user_id, $product_id)
    {
        return self::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->first();
    }


}
