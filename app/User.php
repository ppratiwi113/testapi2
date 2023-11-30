<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

// class User extends Authenticatable implements JWTSubject
// {
//     use Notifiable;

//     public function getJWTIdentifier()
//     {
//         return $this->getKey();
//     }

//     public function getJWTCustomClaims()
//     {
//         return[];
//     }
// }