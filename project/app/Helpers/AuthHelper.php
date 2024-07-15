<?php

namespace App\Helpers;

use App\Models\Socialsetting;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class AuthHelper
{

    public function __construct()
    {
        $this->socialSettings = Socialsetting::findOrFail(1);
        config([
            'services.google' => [
                'client_id' => $this->socialSettings->gclient_id,
                'client_secret' => $this->socialSettings->gclient_secret,
                'redirect' => $this->socialSettings->gredirect,
            ]
        ]);
    }

    public function verifyGoogleToken($token)
    {
        Log::info('Verifying token with Socialite', ['token' => $token]);
        try {
            $socialUser = Socialite::driver('google')->stateless()->userFromToken($token);
            Log::info('Socialite User Retrieved', ['socialUser' => $socialUser]);
            dd($socialUser);
            return $socialUser;
        } catch (\Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }
}

