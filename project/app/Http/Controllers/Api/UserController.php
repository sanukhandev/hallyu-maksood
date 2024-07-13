<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
   // get auth user info

    public function getUserInfo()
    {
        $user =  User::with(['orders','wishlists'])->find(auth()->user());
       if (!$user) {
              return response()->json([
                'status' => 404,
                'message' => 'User not found'
              ]);
       }
         return response()->json([
              'status' => 200,
              'data' => $user
         ]);
    }

}
