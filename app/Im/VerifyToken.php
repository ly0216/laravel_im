<?php

namespace App\Im;

use Tymon\JWTAuth\JWT;

class VerifyToken extends JWT
{

    public function getUser($token)
    {
        try {
            $this->setToken($token);
            $user = $this->parseToken();
            return $user;
        } catch (\Exception $exception) {
            return false;
        }
    }


}


