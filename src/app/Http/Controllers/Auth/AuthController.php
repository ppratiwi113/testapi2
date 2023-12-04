<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWTAuth as JWTAuthJWTAuth;

class AuthController extends Controller
{
    protected $response;
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->response = new Response(); // Gunakan class Response
    }

    public function authenticate(Request $request) {
        $credentials = $request->only('email','password');
        $login = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL)
            ?'email' : 'phone';

        try {
            if($login=='email') {
                $loginCredentials = ['email' => $credentials['username'],
                    'password'=>$credentials['password']];
            }
            else {
                $loginCredentials = ['phone'=>$credentials['username'],
                    'password'=>$credentials['password']];
            }

            if(! $token = JWTAuth::attempt($loginCredentials)) {
                return $this->response()->errorUnauthorized();
            }
        }catch (JWTException $ex){
            return $this->response()->errorInternal();
        }

        return response()->json(compact('token'))->setStatusCode(200);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->toUser()){
                return response()->json(['user not found'], 404);
            }
        }catch (JWTException $e) {
            return $this->response()->errorInternal();
        }
        return $this->response()->item($user, new UserTransformer)->setStatusCode(200);
    }

    /**
    //  * Get a validator for an incoming registration request.
    //  *
    //  * @param  array  $data
    //  * @return \Illuminate\Contracts\Validation\Validator
    //  */
    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'name' => 'required|max:255',
    //         'email' => 'required|email|max:255|unique:users',
    //         'password' => 'required|confirmed|min:6',
    //     ]);
    // }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}