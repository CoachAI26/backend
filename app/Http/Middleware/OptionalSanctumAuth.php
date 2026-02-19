<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class OptionalSanctumAuth
{
    /**
     * If the request has a valid Bearer token, set the user on the request
     * so that $request->user() is available. Does not require authentication.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $request->setUserResolver(fn () => $accessToken->tokenable);
            }
        }

        return $next($request);
    }
}
