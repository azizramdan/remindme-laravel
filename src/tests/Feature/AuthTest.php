<?php

namespace Tests\Feature;

use App\Enums\CommonError;
use App\Enums\TokenName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('invalidLoginRequestProvider')]
    public function testUserCantLoginWithInvalidRequestBody($email, $password)
    {
        $body = array_filter([
            'email' => $email,
            'password' => $password,
        ], fn ($value) => ! is_null($value));

        $this->postJson('/api/session', $body)
            ->assertBadRequest()
            ->assertJsonFragment([
                'ok' => false,
                'err' => CommonError::ERR_BAD_REQUEST,
            ]);
    }

    public static function invalidLoginRequestProvider()
    {
        return [
            ['invalid_email', '123456'],
            ['alice@mail.com', ''],
            ['', '123456'],
            ['alice@mail.com', null],
            [null, '123456'],
        ];
    }

    public function testUserCantLoginBecauseUserNotExists()
    {
        $this->postJson('/api/session', [
            'email' => 'foo@mail.com',
            'password' => '123456',
        ])
            ->assertUnauthorized()
            ->assertJson([
                'ok' => false,
                'err' => CommonError::ERR_INVALID_CREDS->value,
                'msg' => 'incorrect username or password',
            ]);
    }

    public function testUserCantLoginBecausePasswordIsWrong()
    {
        User::factory()->create([
            'email' => 'alice@mail.com',
            'password' => bcrypt('123456'),
        ]);

        $this->postJson('/api/session', [
            'email' => 'alice@mail.com',
            'password' => 'password',
        ])
            ->assertUnauthorized()
            ->assertJson([
                'ok' => false,
                'err' => CommonError::ERR_INVALID_CREDS->value,
                'msg' => 'incorrect username or password',
            ]);
    }

    public function testUserCanLogin()
    {
        User::factory()->create([
            'email' => 'alice@mail.com',
            'password' => bcrypt('123456'),
        ]);

        $this->postJson('/api/session', [
            'email' => 'alice@mail.com',
            'password' => '123456',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'access_token',
                    'refresh_token',
                ],
            ]);
    }

    public function testUserCantRefreshAccessTokenBecauseTokenIsInvalid()
    {
        $this->putJson('/api/session')
            ->assertUnauthorized()
            ->assertJson([
                'ok' => false,
                'err' => CommonError::ERR_INVALID_REFRESH_TOKEN->value,
            ]);

        $this->putJson('/api/session', headers: ['Authorization' => 'Bearer invalid_token'])
            ->assertUnauthorized()
            ->assertJson([
                'ok' => false,
                'err' => CommonError::ERR_INVALID_REFRESH_TOKEN->value,
            ]);
    }

    public function testUserCantRefreshAccessTokenBecauseTokenExpired()
    {
        $user = User::factory()->create([
            'email' => 'alice@mail.com',
            'password' => bcrypt('123456'),
        ]);

        $expiredToken = $user->createToken(TokenName::REFRESH_TOKEN->value, expiresAt: now()->subDay())->plainTextToken;

        $this->withHeaders(['Authorization' => 'Bearer '.$expiredToken])
            ->putJson('/api/session')
            ->assertUnauthorized()
            ->assertJson([
                'ok' => false,
                'err' => CommonError::ERR_INVALID_REFRESH_TOKEN->value,
            ]);
    }

    public function testUserCanRefreshAccessToken()
    {
        $user = User::factory()->create([
            'email' => 'alice@mail.com',
            'password' => bcrypt('123456'),
        ]);

        $refreshToken = $user->createToken(TokenName::REFRESH_TOKEN->value)->plainTextToken;

        $this->withHeaders(['Authorization' => 'Bearer '.$refreshToken])
            ->putJson('/api/session')
            ->assertOk()
            ->assertJsonStructure([
                'ok',
                'data' => [
                    'access_token',
                ],
            ]);
    }
}
