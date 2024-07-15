<?php

namespace App\Helpers;

use App\Models\Socialsetting;

use Google_Client;
use Google_Service_Oauth2;
class AuthHelper
{
    private Google_Client $googleClient;

    public function __construct()
    {
        $this->socialSettings = Socialsetting::findOrFail(1);
        $this->googleClient = new Google_Client();
        $this->googleClient->setClientId($this->socialSettings->gclient_id);
        $this->googleClient->setClientSecret($this->socialSettings->gclient_secret);
        $this->googleClient->setRedirectUri($this->socialSettings->gredirect);
    }


    public function verifyGoogleToken($token){
        $this->googleClient->setAccessToken($token);
//        if ($this->googleClient->isAccessTokenExpired()) {
//            if ($this->googleClient->getRefreshToken()) {
//                $this->googleClient->fetchAccessTokenWithRefreshToken($this->googleClient->getRefreshToken());
//            } else {
//                return false;
//            }
//        }
        $oauth2 = new Google_Service_Oauth2($this->googleClient);
        return $oauth2->userinfo->get();
    }


}
