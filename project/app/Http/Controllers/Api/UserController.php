<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        $this->userId = $request->user()->id;
        $user =  User::with(['orders','wishlists'])->find($this->userId);
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
