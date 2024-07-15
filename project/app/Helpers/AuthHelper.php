<?php

namespace App\Helpers;

use App\Models\Socialsetting;

use Config;
use Google_Client;
use Google_Service_Oauth2;
class AuthHelper
{
    public function __construct(){
        $this->socialSettings = Socialsetting::findOrFail(1);
        Config::set('services.google.client_id', $this->socialSettings->gclient_id);
        Config::set('services.google.client_secret', $this->socialSettings->gclient_secret);
        Config::set('services.google.redirect', url('/auth/google/callback'));
        Config::set('services.facebook.client_id', $this->socialSettings->fclient_id);
        Config::set('services.facebook.client_secret', $this->socialSettings->fclient_secret);
        $url = url('/auth/facebook/callback');
        $url = preg_replace("/^http:/i", "https:", $url);
        Config::set('services.facebook.redirect', $url);
        $this->googleClient = new Google_Client(['client_id' => config('services.google.client_id')]);

    }

    public function verifyGoogleToken($token){
        $this->googleClient->setAccessToken($token);
        if ($this->googleClient->isAccessTokenExpired()) {
            return false;
        }
        $oauth2 = new Google_Service_Oauth2($this->googleClient);
        return $oauth2->userinfo->get();
    }


}
