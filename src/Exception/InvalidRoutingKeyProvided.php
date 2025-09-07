<?php

declare(strict_types=1);

namespace Vehis\Msc\Exception;

use Throwable;

class InvalidRoutingKeyProvided extends MscException
{
    public function __construct(string $routingKey = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Invalid routing key has been provided: %s', $routingKey),
            $code,
            $previous
        );
    }
}
