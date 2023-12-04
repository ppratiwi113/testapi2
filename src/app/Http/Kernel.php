<?php

namespace App\Http;

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        // \LucaDegasperi\OAuth2Server\Middleware\OAuthExceptionHandlerMiddleware::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'auth-api' => \App\Http\Middleware\AuthApiMiddleware::class,
        // 'csrf.api' => \App\Http\Middleware\VerifyCsrfTokenApi::class,
        // 'jwt.auth' => \Tymon\JWTAuth\Middleware\GetUserFromToken::class,
        'jwt-auth' => \App\Http\Middleware\JwtMiddleware::class,
        'jwt.refresh' =>\Tymon\JWTAuth\Middleware\RefreshToken::class,
        // 'oauth' =>\LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware::class,
        // 'oauth-user' =>\LucaDegasperi\OAuth2Server\Middleware\OAuthUserOwnerMiddleware::class,
        // 'oauth-client'=>\LucaDegasperi\OAuth2Server\Middleware\OAuthClientOwnerMiddleware::class,
        // 'check-authorization-params' =>\LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware::class,
    ];

    protected $middlewareGroups =[
        'api' => [
            \App\Http\Middleware\JwtMiddleware::class,
        ],
    ];
}