<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auth extends Model
{
    public static function authenticate($credentials)
    {
        // Implementasi logika autentikasi di sini.
        // Contoh sederhana, bisa berupa validasi hardcoded atau validasi dengan DB lain.

        $validCredentials = [
            'username' => 'user123',
            'password' => 'password123',
        ];

        if ($credentials['username'] === $validCredentials['username'] &&
            $credentials['password'] === $validCredentials['password']) {
            return true;
            }
            return false;
    }
}