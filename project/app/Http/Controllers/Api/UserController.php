<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
   // get auth user info

    public function getUserInfo(Request $request)
    {
        $user_id = $request->user()->id;
        dd($user_id);
        $user =  User::with(['orders','wishlists'])->find($user_id);
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
