<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{


  private $userId;

  public function __construct()
  {
    $this->userId = null;
  }
  // get auth user info

  public function getUserInfo(Request $request)
  {
    $userId = $request->user()->id;
    $user = User::with(['orders', 'wishlists'])->find($userId);

    if (!$user) {
      return response()->json([
        'status' => 404,
        'message' => 'User not found'
      ]);
    }

    if ($user->orders->isNotEmpty()) {
      $user->orders->each(function ($order) {
        $order->cart = json_decode($order->cart);
      });
    }

    return response()->json([
      'status' => 200,
      'data' => $user
    ]);
  }
}
