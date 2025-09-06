<?php

declare(strict_types=1);

namespace Vehis\Msc\Message;

use DateTimeImmutable;

abstract class Message
{
    public function supportedTo(): ?DateTimeImmutable
    {
        return null;
    }

    /**
     * Override if needed, otherwise the routing key will be generated
     */
    public function getRoutingKey(): ?string
    {
        return null;
    }
}
