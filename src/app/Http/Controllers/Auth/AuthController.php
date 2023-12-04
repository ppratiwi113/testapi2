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
            'exp' => time() + (60 * 60),
            // ...Tambahkan data tambahan yang ingin dimasukkan ke dalam token JWT
        ];
        return JWT::encode($payload, $secretKey, 'HS256');
    }
}

// class AuthController extends Controller
// {
    // protected $response;
    // public function __construct()
    // {
    //     $this->middleware('guest', ['except' => 'getLogout']);
    //     // $this->response = new Response(); // Gunakan class Response
    // }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('username','password');

    //     if(!Auth::authenticate($credentials)) {
    //         return response()->json(['error'=>'Unauthorized', 401]);
    //     }

    //     $token = JWTAuth::fromUser($request->username);
    //     return response()->json(compact('token'));

        // try{
        //     if(!$token=JWTAuth::attempt($credentials)) {
        //         return response()->json(['error'=>'invalid_credentials', 401]);
        //     }
        // } catch(JWTException $e) {
        //     //Something went wrong!
        //     return response()->json(['error'=>'could_not_create_token'], 500);
        // }

        

        //// if no errors are encountered we can return a JWT
        
    // 

    // public function getAuthenticatedUser() {
    //     try {
    //         if(!$user = JWTAuth::parseToken()->authenticate()) {
    //             return response()->json(['user_not_found'], 404);
    //         }
    //     }catch (TokenExpiredException $e) {
    //         return response()->json(['token_expired'], $e->getStatusCode());
    //     }catch (TokenInvalidException $e) {
    //         return response()->json(['token_invalid'], $e->getStatusCode());
    //     }catch (JWTException $e) {
    //         return response()->json(['token_absent'], $e->getStatusCode());
    //     }

    //     return response()->json(compact('user'));
    // }

    // public function register(Request $request)
    // {
    //     $newuser = $request->all();
    //     $password = Hash::make($request->input('password'));
    //     $newuser['password'] = $password;
    //     return User::create($newuser);
    // }

    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => bcrypt($data['password']),
    //     ]);
    // }