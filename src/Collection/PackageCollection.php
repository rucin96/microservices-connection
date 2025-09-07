<?php

declare(strict_types=1);

namespace Vehis\Msc\Collection;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use Vehis\Msc\Message\Package;
use Vehis\Msc\VO\RoutingKey;

class PackageCollection implements IteratorAggregate, Countable
{
    /**
     * @var RoutingKey[]
     */
    private array $routingKeysList = [];

    /**
     * @var Package[]
     */
    private array $packages = [];

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

    public function list(): array
    {
        return $this->packages;
    }

    /**
     * @return string[]
     */
    public function getRoutingKeysAsStringList(): array
    {
        return array_map(fn (RoutingKey $routingKey) => (string) $routingKey, $this->routingKeysList);
    }

    /**
     * @return Traversable<Package>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->packages);
    }

    public function count(): int
    {
        return count($this->packages);
    }
}
