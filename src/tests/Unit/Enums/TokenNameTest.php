<?php

namespace Tests\Unit\Enums;

use App\Enums\TokenName;
use PHPUnit\Framework\TestCase;

class TokenNameTest extends TestCase
{
    public function testTokenNameCases()
    {
        $this->assertCount(2, TokenName::cases());
        $this->assertSame('access-token', TokenName::ACCESS_TOKEN->value);
        $this->assertSame('refresh-token', TokenName::REFRESH_TOKEN->value);
    }
}
