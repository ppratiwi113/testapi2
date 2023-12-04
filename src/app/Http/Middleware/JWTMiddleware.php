<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');


        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $token = str_replace('Bearer ', '', $token);
            $secretKey = env('JWT_SECRET');

            //Decode token JWT
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            //izinkan akses jikan username "user123"
            if ($decoded->username === 'user123') {
                return $next($request);
            } else {
                return response()->json(['error' => 'Unauthorized access'], 403);
            } 

        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }
}