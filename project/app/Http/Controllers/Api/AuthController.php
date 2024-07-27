<?php

namespace app\Http\Controllers\Api;

use App\Helpers\{
    FirebaseServiceProvider,
    NotificationHelper
};
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
    private $notification;

    public function __construct()
    {
        $this->firebaseServiceProvider = new FirebaseServiceProvider();
        $this->notification = new NotificationHelper();
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

    private function loginExistingUser($user): \Illuminate\Http\JsonResponse
    {
        Auth::login($user);
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

    public function validate_or_send_otp(Request $request)
    {
        $rules = [
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'verify_otp' => 'sometimes|min:4|max:4',
        ];
        $request->validate($rules);

        if ($request->phone && !$request->verify_otp) {
            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                //create user
                $user = new User();
                $user->phone = $request->phone;
                $user->name = 'User';
                $user->email = "user{$request->phone}@hallyu.com";
                $user->password = Hash::make('123456');
                $user->email_verified = 'Yes';
                $user->status = 1;
                $user->genarateOTP();
                $user->save();
            }else{
                $user->genarateOTP();
                $user->save();
            }
            $res = $this->notification->sendSms($user->phone, `Your OTP is: ${$user->otp}`);
            return response()->json([
                'status' => 200,
                'message' => 'OTP sent successfully',
                'debug' => $res
            ]);
        } else if ($request->phone && $request->verify_otp) {
            return $this->verifyOTP($request->phone, $request->verify_otp);
        }

        return response()->json([
            'status' => 400,
            'message' => 'Invalid request'
        ]);
    }


    private function sendOTPSMS($phone, $otp)
    {
        // Code to send SMS using a third-party service

        return response()->json(
            [
                'status' => 200,
                'message' => 'OTP sent'
            ]
        );
    }

    private function verifyOTP($phone, $otp)
    {
        $user = User::where('phone', $phone)->first();

        if ($user && $user->otp == $otp) {
            return response()->json([
                'status' => 200,
                'message' => 'OTP verified successfully'
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'Invalid OTP'
        ]);
    }


}
