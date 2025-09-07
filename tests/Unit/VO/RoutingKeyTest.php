<?php

declare(strict_types=1);

namespace Tests\Vehis\Msc\Unit\VO;

use Tests\Vehis\Msc\Unit\MscTestCase;
use Vehis\Msc\Exception\InvalidRoutingKeyProvided;
use Vehis\Msc\VO\RoutingKey;

class RoutingKeyTest extends MscTestCase
{
    public function testCanCreateValidRoutingKey(): void
    {
        $key = 'publisher.event.name';
        $routingKey = new RoutingKey($key);

        $this->assertSame($key, $routingKey->key);
        $this->assertSame($key, (string)$routingKey);
    }
    public function testCanCreateRoutingKeyWithNumberAtTheEnd(): void
    {
        $key = 'publisher.event.name.v1';
        $routingKey = new RoutingKey($key);

        $this->assertSame($key, $routingKey->key);
        $this->assertSame($key, (string)$routingKey);
    }

    public function testInvalidRoutingKeyThrowsException(): void
    {
        $this->expectException(InvalidRoutingKeyProvided::class);

        new RoutingKey('invalid_key');
    }

    public function testAnotherInvalidKeyThrowsException(): void
    {
        $this->expectException(InvalidRoutingKeyProvided::class);

        new RoutingKey('123.Invalid.Key');
    }
}
