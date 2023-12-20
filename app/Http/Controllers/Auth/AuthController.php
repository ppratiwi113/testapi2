<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $providedUsername = $request->input('username');
        $providedPassword = $request->input('password');

        if ($this->isValidCredentials($providedUsername, $providedPassword)) {
            $token = $this->generateToken($providedUsername);
            return response()->json(['token' => $token]);
        } else {
            return response()->json(['error' => 'Authentication failed'], 401);
        }
    }

    private function isValidCredentials($providedUsername, $providedPassword)
    {
        $username = 'user123';
        $password = 'password123';

        return $providedUsername === $username && $providedPassword === $password;
    }

    private function generateToken($username)
    {
        $secretKey = env('JWT_SECRET'); // Ganti dengan secret key yang sebenarnya
        $payload = [
            'username' => $username,
            'exp' => time() + (30 * 24 * 60 * 60) // 30 hari * 24 jam * 60 menit * 60 detik (satu bulan)
            // ...Tambahkan data tambahan yang ingin dimasukkan ke dalam token JWT
        ];
        return JWT::encode($payload, $secretKey, 'HS256');
    }
}