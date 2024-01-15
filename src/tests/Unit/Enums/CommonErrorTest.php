<?php

namespace Tests\Unit\Enums;

use App\Enums\CommonError;
use PHPUnit\Framework\TestCase;

class CommonErrorTest extends TestCase
{
    public function testCommonErrorCases()
    {
        $this->assertCount(7, CommonError::cases());
        $this->assertSame('ERR_INVALID_CREDS', CommonError::ERR_INVALID_CREDS->value);
        $this->assertSame('ERR_INVALID_REFRESH_TOKEN', CommonError::ERR_INVALID_REFRESH_TOKEN->value);
        $this->assertSame('ERR_BAD_REQUEST', CommonError::ERR_BAD_REQUEST->value);
        $this->assertSame('ERR_INVALID_ACCESS_TOKEN', CommonError::ERR_INVALID_ACCESS_TOKEN->value);
        $this->assertSame('ERR_FORBIDDEN_ACCESS', CommonError::ERR_FORBIDDEN_ACCESS->value);
        $this->assertSame('ERR_NOT_FOUND', CommonError::ERR_NOT_FOUND->value);
        $this->assertSame('ERR_INTERNAL_ERROR', CommonError::ERR_INTERNAL_ERROR->value);
    }
}
