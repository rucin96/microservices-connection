<?php

declare(strict_types=1);

namespace Vehis\Msc\VO;

use Vehis\Msc\Exception\InvalidRoutingKeyProvided;

class RoutingKey
{
    /**
     * @throws InvalidRoutingKeyProvided
     */
    public function __construct(
        public string $key
    ) {
        if (false === preg_match('/^[a-z]+(\.[a-z0-9]+)+$/', $this->key)) {
            throw new InvalidRoutingKeyProvided($this->key);
        }
    }

    public function __toString(): string
    {
        return $this->key;
    }
}
