<?php

declare(strict_types=1);

namespace Vehis\Msc\Generator;

use ReflectionClass;
use ReflectionException;
use Vehis\Msc\Exception\CannotGenerateRoutingKeyException;
use Vehis\Msc\Exception\InvalidRoutingKeyProvided;
use Vehis\Msc\VO\RoutingKey;

readonly class RoutingKeyGenerator
{
    public function __construct(
        private string $publisherName,
    ) {
    }

    /**
     * @throws CannotGenerateRoutingKeyException
     * @throws InvalidRoutingKeyProvided
     */
    public function generateKey(string $className): RoutingKey
    {
        if (empty($className)) {
            throw new CannotGenerateRoutingKeyException('className parameter cannot be null');
        }

        try {
            $shortName = (new ReflectionClass($className))->getShortName();
        } catch (ReflectionException $e) {
            throw new CannotGenerateRoutingKeyException(previous: $e);
        }

        $words = preg_split('/(?=[A-Z])/', $shortName, -1, PREG_SPLIT_NO_EMPTY);
        $words = array_map('strtolower', $words);
        $key = implode('.', $words);

        return $this->decorateKey($key);
    }

    /**
     * @throws InvalidRoutingKeyProvided
     */
    public function decorateKey(string $key): RoutingKey
    {
        if (empty($key)) {
            throw new InvalidRoutingKeyProvided($key);
        }

        return new RoutingKey(sprintf('%s.%s', $this->publisherName, $key));
    }
}
