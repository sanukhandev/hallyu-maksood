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
        $this->googleClient->setScopes('email');

    }


    public function verifyGoogleToken($token){
       // $token is JWT token from google how to validate
       $res=  $this->googleClient->verifyIdToken($token);
           dd($res);
       if ($res) {
           return $res;
       }
         return false;


    }


}
