<?php

declare(strict_types=1);

namespace Vehis\MsC\Generator;

use ReflectionClass;
use ReflectionException;
use Vehis\MsC\Exception\CannotGenerateRoutingKeyException;

readonly class RoutingKeyGenerator
{
    public function __construct(
        private string $publisherName,
    ) {
    }

    /**
     * @throws CannotGenerateRoutingKeyException
     */
    public function generateKey(string $className): string
    {
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

    public function decorateKey(string $key): string
    {
        return sprintf('%s.%s', $this->publisherName, $key);
    }
}
