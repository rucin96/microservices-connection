<?php

declare(strict_types=1);

namespace Tests\Vehis\Msc\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Vehis\Msc\Message\BackwardCompatibleInterface;
use Vehis\Msc\Message\Message;
use Vehis\Msc\Message\Package;
use Tests\Vehis\Msc\TestDoubles\Dummies\Message\DummyEvent;
use Vehis\Msc\VO\RoutingKey;

class MscTestCase extends TestCase
{
    protected function generatePackage(string $routingKey): Package
    {
        return new Package(
            DummyEvent::generate(),
            new RoutingKey($routingKey)
        );
    }

    protected function getAnonymousMessage(
        ?string $routingKey = null,
        ?DateTimeImmutable $supportedTo = null,
    ): Message
    {
        return new class($routingKey, $supportedTo) extends Message {
            public function __construct(
                private ?string $routingKey = null,
                private ?DateTimeImmutable $supportedTo = null,
            ) {}

            public function getRoutingKey(): ?string
            {
                return $this->routingKey;
            }

            public function supportedTo(): ?DateTimeImmutable
            {
                return $this->supportedTo;
            }
        };
    }

    protected function getAnonymousMessageWithBackwardCompatible(
        Message $previousMessage,
        ?string $routingKey = null,
        ?DateTimeImmutable $supportedTo = null,
    ): Message
    {
        return new class($previousMessage, $routingKey, $supportedTo) extends Message implements BackwardCompatibleInterface {
            public function __construct(
                private Message $previousMessage,
                private ?string $routingKey = null,
                private ?DateTimeImmutable $supportedTo = null,
            ) {}

            public function getRoutingKey(): ?string
            {
                return $this->routingKey;
            }

            public function supportedTo(): ?DateTimeImmutable
            {
                return $this->supportedTo;
            }

            public function getPreviousVersion(): Message
            {
                return $this->previousMessage;
            }
        };
    }
}
