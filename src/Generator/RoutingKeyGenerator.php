<?php

declare(strict_types=1);

namespace Vehis\Msc\Generator;

use ReflectionClass;
use ReflectionException;
use Vehis\Msc\Exception\CannotGenerateRoutingKeyException;

class RoutingKeyGenerator
{
    /**
     * @throws CannotGenerateRoutingKeyException
     */
    public function generateKey(string $className, ?string $publisherName = null): string
    {
        try {
            $shortName = (new ReflectionClass($className))->getShortName();
        } catch (ReflectionException $e) {
            throw new CannotGenerateRoutingKeyException(previous: $e);
        }

        $words = preg_split('/(?=[A-Z])/', $shortName, -1, PREG_SPLIT_NO_EMPTY);
        $words = array_map('strtolower', $words);

        if ($publisherName) {
            array_unshift($words, $publisherName);
        }

        return implode('.', $words);
    }
}
