<?php

namespace App\Helpers;

use App\Models\Socialsetting;

use Config;
use Google_Client;
use Google_Service_Oauth2;
class AuthHelper
{
    private Google_Client $googleClient;

    public function __construct(){
        $this->socialSettings = Socialsetting::findOrFail(1);
        $this->googleClient = new Google_Client();

        Config::set('services.google.client_id', $this->socialSettings->google_client_id);
        Config::set('services.google.client_secret', $this->socialSettings->google_client_secret);
        Config::set('services.google.redirect', $this->socialSettings->google_redirect);

        $this->googleClient->setClientId(config('services.google.client_id'));
        $this->googleClient->setClientSecret(config('services.google.client_secret'));
        $this->googleClient->setRedirectUri(config('services.google.redirect'));
        $this->googleClient = new Google_Client(['client_id' => config('services.google.client_id')]);

    }

    public function verifyGoogleToken($token){
        // show config
         dd(config('services.google'));

        $this->googleClient->setAccessToken($token);
        if ($this->googleClient->isAccessTokenExpired()) {
            if ($this->googleClient->getRefreshToken()) {
                $this->googleClient->fetchAccessTokenWithRefreshToken($this->googleClient->getRefreshToken());
            } else {
                return false;
            }
        }
        $oauth2 = new Google_Service_Oauth2($this->googleClient);
        return $oauth2->userinfo->get();
    }


}
