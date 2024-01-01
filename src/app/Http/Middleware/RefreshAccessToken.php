<?php

namespace App\Http\Middleware;

use App\Enums\CommonError;
use App\Enums\TokenName;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

class RefreshAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Auth::setDefaultDriver('sanctum');

        Sanctum::authenticateAccessTokensUsing(function (PersonalAccessToken $accessToken, bool $isValid) {
            return $isValid && $accessToken->name === TokenName::REFRESH_TOKEN->value;
        });

        if (! Auth::check()) {
            return response()->json([
                'ok' => false,
                'err' => CommonError::ERR_INVALID_REFRESH_TOKEN,
                'msg' => 'invalid refresh token',
            ], HttpResponse::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
