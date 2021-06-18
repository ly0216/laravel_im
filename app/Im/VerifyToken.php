<?php

namespace App\Im;

use Tymon\JWTAuth\JWTAuth;

class VerifyToken extends JWTAuth
{

    public function getUser($token)
    {
        try{
            $this->setToken($token);
            $user = $this->authenticate();
            return $user;
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

}
