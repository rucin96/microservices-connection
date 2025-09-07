<?php

declare(strict_types=1);

namespace Vehis\Msc\Exception;

use Throwable;

class DispatchMessageStoppedException extends MscException
{
    public function __construct(
        string $stoppedRoutingKey,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            sprintf('Publishing stopped when try to process routing key: %s', $stoppedRoutingKey),
            $code,
            $previous
        );
    }
}
