<?php

namespace Tests\Unit;

use App\Enums\CommonError;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BaseJsonResponseTest extends TestCase
{
    public function testBaseSuccessResponseWithoutParams()
    {
        $reflection = new ReflectionClass(Controller::class);
        $method = $reflection->getMethod('success');
        $method->setAccessible(true);

        $controller = new Controller;
        $response = $method->invoke($controller);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertEquals((object) ['ok' => true], $response->getData());
    }

    public function testBaseSuccessResponseWithDataAndStatus()
    {
        $reflection = new ReflectionClass(Controller::class);
        $method = $reflection->getMethod('success');
        $method->setAccessible(true);

        $data = [
            'id' => 1,
            'name' => 'alice',
        ];

        $controller = new Controller;
        $response = $method->invokeArgs($controller, [$data, Response::HTTP_CREATED]);

        $this->assertEquals(Response::HTTP_CREATED, $response->status());
        $this->assertEquals((object) ['ok' => true, 'data' => (object) $data], $response->getData());
    }

    public function testBaseFailResponseWithDefaultStatus()
    {
        $reflection = new ReflectionClass(Controller::class);
        $method = $reflection->getMethod('fail');
        $method->setAccessible(true);

        $args = [
            CommonError::ERR_BAD_REQUEST,
            'error message',
        ];

        $controller = new Controller;
        $response = $method->invokeArgs($controller, $args);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertEquals((object) [
            'ok' => false,
            'err' => $args[0]->value,
            'msg' => $args[1],
        ], $response->getData());
    }

    public function testBaseFailResponseWithCustomStatus()
    {
        $reflection = new ReflectionClass(Controller::class);
        $method = $reflection->getMethod('fail');
        $method->setAccessible(true);

        $args = [
            CommonError::ERR_BAD_REQUEST,
            'error message',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        ];

        $controller = new Controller;
        $response = $method->invokeArgs($controller, $args);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
        $this->assertEquals((object) [
            'ok' => false,
            'err' => $args[0]->value,
            'msg' => $args[1],
        ], $response->getData());
    }
}
