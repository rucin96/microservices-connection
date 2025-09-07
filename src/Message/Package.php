<?php

declare(strict_types=1);

namespace Vehis\Msc\Message;

use Vehis\Msc\VO\RoutingKey;

readonly class Package
{
    public function __construct(
        public Message $message,
        public RoutingKey $routingKey,
    ) {
    }
}
