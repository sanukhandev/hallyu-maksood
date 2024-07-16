<?php

namespace App\Helpers;

use Kreait\Firebase\Factory;
use PHPUnit\Exception;

class FirebaseServiceProvider
{

    protected $auth;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path('hallyu-aa242-firebase-adminsdk-hkju7-bdd64bf6f7.json'));

        $this->auth = $firebase->createAuth();
    }

    public function verifyIdToken($idToken)
    {
        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            return $verifiedIdToken->claims()->data();
        } catch (Exception $e) {
            return false;
        }
    }

}


