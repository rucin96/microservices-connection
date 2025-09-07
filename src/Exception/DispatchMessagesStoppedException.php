<?php

declare(strict_types=1);

namespace Vehis\Msc\Exception;

use Throwable;

class DispatchMessagesStoppedException extends MscException
{
    /**
     * @var string[]
     */
    private array $stoppedRoutingKeys;

    public function __construct(
        array $stoppedRoutingKeys,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct('Dispatching messages stopped', $code, $previous);
        $this->stoppedRoutingKeys = $stoppedRoutingKeys;
    }

    public function getStoppedRoutingKeys(): array
    {
        return $this->stoppedRoutingKeys;
    }
}
