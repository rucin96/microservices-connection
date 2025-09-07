<?php

declare(strict_types=1);

namespace Tests\Vehis\Msc\Unit\Collection;

use Tests\Vehis\Msc\Unit\MscTestCase;
use Vehis\Msc\Collection\PackageCollection;

class PackageCollectionTest extends MscTestCase
{
    public function testCreateEmptyCollection(): void
    {
        $collection = new PackageCollection();

        $this->assertEmpty($collection->list());
        $this->assertCount(0, $collection);
    }

    public function testCreateOneMessageCollection(): void
    {
        $collection = new PackageCollection();
        $package = $this->generatePackage('dummy.key');

        $collection->add($package);

        $this->assertCount(1, $collection);
        $this->assertSame([$package], $collection->list());
    }

    public function testCreateFewMessagesCollection(): void
    {
        $collection = new PackageCollection();

        $package1 = $this->generatePackage('dummy.key1');
        $collection->add($package1);
        $package2 = $this->generatePackage('dummy.key2');
        $collection->add($package2);

        $this->assertCount(2, $collection);
        $this->assertSame([$package1, $package2], $collection->list());
    }

    public function testCreateFewSameMessagesCollection(): void
    {
        $collection = new PackageCollection();
        $package = $this->generatePackage('dummy.key');

        $collection->add($package);
        $collection->add($package);

        $this->assertCount(2, $collection);
        $this->assertSame([$package, $package], $collection->list());
    }

    public function testListRoutingKeysShouldReturnEmptyArrayWhenCollectionIsEmpty(): void
    {
        $collection = new PackageCollection();

        $this->assertEquals([], $collection->getRoutingKeysAsStringList());
    }

    public function testListRoutingKeysShouldReturnSameKeysAsCollectionItems(): void
    {
        $collection = new PackageCollection();

        $package1 = $this->generatePackage('dummy.key1');
        $collection->add($package1);
        $package2 = $this->generatePackage('dummy.key2');
        $collection->add($package2);

        $this->assertEquals([
            (string) $package1->routingKey,
            (string) $package2->routingKey
        ], $collection->getRoutingKeysAsStringList());
    }

    public function testCollectionShouldBeIterable(): void
    {
        $collection = new PackageCollection();

        $this->assertIsIterable($collection);
    }

    public function testCollectionShouldServePackageAsIterable(): void
    {
        $collection = new PackageCollection();
        $package1 = $this->generatePackage('dummy.key1');
        $collection->add($package1);
        $package2 = $this->generatePackage('dummy.key2');
        $collection->add($package2);

        foreach ($collection as $package) {
            $this->assertContainsEquals($package->routingKey, [$package1->routingKey, $package2->routingKey]);
        }
    }
}
