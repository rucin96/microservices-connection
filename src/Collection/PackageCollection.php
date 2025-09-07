<?php

declare(strict_types=1);

namespace Vehis\Msc\Collection;

use ArrayIterator;
use IteratorAggregate;
use Traversable;
use Vehis\Msc\Message\Package;
use Vehis\Msc\VO\RoutingKey;

class PackageCollection implements IteratorAggregate
{
    /**
     * @var RoutingKey[]
     */
    private array $routingKeysList;

    /**
     * @var Package[]
     */
    private array $packages;

    public function __construct(Package ...$packages)
    {
        foreach ($packages as $package) {
            $this->add($package);
        }
    }

    public function add(Package $package): void
    {
        $this->packages[] = $package;
        $this->routingKeysList[] = $package->routingKey;
    }

    /**
     * @return string[]
     */
    public function getRoutingKeysAsStringList(): array
    {
        return array_map(fn (RoutingKey $routingKey) => (string) $routingKey, $this->routingKeysList);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->packages);
    }
}
