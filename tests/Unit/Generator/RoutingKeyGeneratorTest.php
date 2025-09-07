<?php

declare(strict_types=1);

namespace Tests\Vehis\Msc\Unit\Generator;

use Tests\Vehis\Msc\Unit\MscTestCase;
use Vehis\Msc\Exception\CannotGenerateRoutingKeyException;
use Vehis\Msc\Exception\InvalidRoutingKeyProvided;
use Vehis\Msc\Generator\RoutingKeyGenerator;
use Vehis\Msc\VO\RoutingKey;

class RoutingKeyGeneratorTest extends MscTestCase
{
    private RoutingKeyGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new RoutingKeyGenerator('vehis');
    }

    public function testGenerateKeyFromClassName(): void
    {
        $key = $this->generator->generateKey(\DateTimeImmutable::class);

        $this->assertInstanceOf(RoutingKey::class, $key);
        $this->assertSame('vehis.date.time.immutable', (string)$key);
    }

    public function testGenerateKeyFromSingleWordClass(): void
    {
        $key = $this->generator->generateKey(\stdClass::class);

        $this->assertSame('vehis.std.class', (string)$key);
    }

    public function testGenerateKeyThrowsExceptionForInvalidClass(): void
    {
        $this->expectException(CannotGenerateRoutingKeyException::class);

        $this->generator->generateKey('ClassThatDoesNotExist');
    }

    public function testDecorateKeyReturnsRoutingKey(): void
    {
        $key = $this->generator->decorateKey('custom.key');

        $this->assertInstanceOf(RoutingKey::class, $key);
        $this->assertSame('vehis.custom.key', (string)$key);
    }

    public function testDecorateKeyThrowsForInvalidEmptyKey(): void
    {
        $this->expectException(InvalidRoutingKeyProvided::class);

        $this->generator->decorateKey('');
    }

    public function testGenerateKeyThrowsForInvalidEmptyKey(): void
    {
        $this->expectException(CannotGenerateRoutingKeyException::class);

        $this->generator->generateKey('');
    }
}
