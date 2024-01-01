<?php

namespace App\Http\Controllers;

use App\Enums\CommonError;
use App\Enums\TokenName;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (! Auth::attempt($validated)) {
            return $this->fail(CommonError::ERR_INVALID_CREDS, 'incorrect username or password', Response::HTTP_UNAUTHORIZED);
        }

        /** @var User */
        $user = Auth::user();

        $accessToken = $this->createAccessToken($user);
        $refreshToken = $user->createToken(TokenName::REFRESH_TOKEN->value, expiresAt: ($exp = config('sanctum.refresh_token_expiration')) ? now()->addSeconds($exp) : null)->plainTextToken;

        return $this->success([
            'user' => $user->only('id', 'name', 'email'),
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
    }

    public function refreshToken()
    {
        /** @var User */
        $user = Auth::user();

        $accessToken = $this->createAccessToken($user);

        return $this->success([
            'access_token' => $accessToken,
        ]);
    }

    private function createAccessToken(User $user): string
    {
        return $user->createToken(TokenName::ACCESS_TOKEN->value, expiresAt: now()->addSeconds(config('sanctum.access_token_expiration')))->plainTextToken;
    }
}
