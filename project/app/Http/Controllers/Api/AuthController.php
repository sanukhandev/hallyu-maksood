<?php

namespace app\Http\Controllers\Api;

use App\Helpers\FirebaseServiceProvider;
use App\Http\Controllers\Controller;
use App\Models\SocialProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $firebaseServiceProvider;

    public function __construct()
    {
        $this->firebaseServiceProvider = new FirebaseServiceProvider();
    }

    public function handleProviderCallback(Request $request, $provider): \Illuminate\Http\JsonResponse
    {
        try {
            $socialUser = $this->getSocialUser($request, $provider);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to authenticate user',
                'message' => $e->getMessage(),
            ], 401);
        }

        if (!$socialUser) {
            return response()->json([
                'error' => 'Auth Expired - please login again',
            ], 401);
        }

        return $this->processUser($socialUser, $provider);
    }

    private function getSocialUser(Request $request, $provider)
    {
        if ($provider === 'google') {
            return $this->firebaseServiceProvider->verifyIdToken($request->token);
        }

        return Socialite::driver($provider)->stateless()->user();
    }

    private function processUser($socialUser, $provider): \Illuminate\Http\JsonResponse
    {
        $socialProvider = SocialProvider::where('provider_id', $socialUser['user_id'])->first();

        if (!$socialProvider) {
            return $this->registerOrLoginUser($socialUser, $provider);
        }

        return $this->loginExistingUser($socialProvider->user);
    }

    private function registerOrLoginUser($socialUser, $provider): \Illuminate\Http\JsonResponse
    {
        $existingUser = User::where('email', $socialUser['email'])->first();

        if ($existingUser) {
            return $this->loginUserWithProvider($existingUser, $socialUser['user_id'], $provider);
        }

        return $this->registerNewUser($socialUser, $provider);
    }

    private function loginUserWithProvider($user, $providerId, $provider): \Illuminate\Http\JsonResponse
    {
        Auth::login($user);
        $token = $user->createToken('hallYuApp')->plainTextToken;

        $socialProvider = new SocialProvider();
        $socialProvider->provider_id = $providerId;
        $socialProvider->provider = $provider;
        $socialProvider->user_id = $user->id;
        $socialProvider->save();

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    private function registerNewUser($socialUser, $provider): \Illuminate\Http\JsonResponse
    {
        $user = new User();
        $user->name = $socialUser['name'];
        $user->email = $socialUser['email'];
        $user->password = Hash::make('123456');
        $user->email_verified = 'Yes';
        $user->status = 1;
        $user->save();

        $socialProvider = new SocialProvider();
        $socialProvider->provider_id = $socialUser['user_id'];
        $socialProvider->provider = $provider;
        $socialProvider->user_id = $user->id;
        $socialProvider->save();

        $token = $user->createToken('hallYuApp')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    private function loginExistingUser($email, $password): \Illuminate\Http\JsonResponse
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $token = $user->createToken('hallYuApp')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }


    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'User already exists'], 401);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->email_verified = 'No';
        $user->status = 1;
        $user->save();

        $token = $user->createToken('hallYuApp')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('hallYuApp')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
