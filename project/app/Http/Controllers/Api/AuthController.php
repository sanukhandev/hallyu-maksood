<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialProvider;
use App\Models\Socialsetting;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->socialSettings = Socialsetting::findOrFail(1);

    }


    public function handleProviderCallback(Request $request, $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to authenticate user'], 401);
        }

        $socialProvider = SocialProvider::where('provider_id', $socialUser->getId())->first();

        if (!$socialProvider) {
            $existingUser = User::where('email', $socialUser->email)->first();

            if ($existingUser) {
                Auth::login($existingUser);
                $token = $existingUser->createToken('hallYuApp')->accessToken;

                return response()->json([
                    'token' => $token,
                    'user' => $existingUser,
                ]);
            }

            $user = new User;
            $user->email = $socialUser->email;
            $user->name = $socialUser->name;
            $user->password = bcrypt('123456');
            $user->status = 1;
            $user->email_verified = 'Yes';
            $user->save();

            $socialProvider = new SocialProvider;
            $socialProvider->provider_id = $socialUser->getId();
            $socialProvider->provider = $provider;
            $socialProvider->user_id = $user->id;
            $socialProvider->save();

            $token = $user->createToken('hallYuApp')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ], 201);
        } else {
            $user = $socialProvider->user;
            Auth::login($user);
            $token = $user->createToken('hallYuApp')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        }
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json(['error' => 'User already exists'], 401);
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->email_verified = 'No';
        $user->status = 1;
        $user->save();

        $token = $user->createToken('hallYuApp')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('hallYuApp')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
