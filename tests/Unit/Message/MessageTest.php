<?php

declare(strict_types=1);

namespace Tests\Vehis\Msc\Unit\Message;

use Tests\Vehis\Msc\Unit\MscTestCase;

class MessageTest extends MscTestCase
{
    public function testSupportedToReturnsNullByDefault(): void
    {
        $message = $this->getAnonymousMessage();

        $this->assertNull($message->supportedTo());
    }

    public function testHasRoutingKeyReturnsFalseIfNoKey(): void
    {
        $message = $this->getAnonymousMessage();

        $this->assertFalse($message->hasRoutingKey());
    }

    public function testHasRoutingKeyReturnsTrueIfKeyProvided(): void
    {
        $message = $this->getAnonymousMessage('vehis.test.key');

        $this->assertTrue($message->hasRoutingKey());
        $this->assertSame('vehis.test.key', $message->getRoutingKey());
    }
}
