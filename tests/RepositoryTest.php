<?php
declare(strict_types=1);

namespace Ueef\Machina\Tests;

use PHPUnit\Framework\TestCase;
use Ueef\Machina\Drivers\NullDriver;
use Ueef\Machina\Exceptions\CannotLockException;
use Ueef\Machina\Interfaces\DriverInterface;
use Ueef\Machina\Interfaces\FilterInterface;
use Ueef\Machina\Interfaces\MetadataInterface;
use Ueef\Machina\Metadata;
use Ueef\Machina\Repository;

class RepositoryTest extends TestCase
{
    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testGet(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $filters = [];
        $orders = [];
        $offset = 0;

        $item = $repository->get($filters, $orders, $offset);

        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['filters'], $filters);
        $this->assertEquals($temp['orders'], $orders);
        $this->assertEquals($temp['limit'], 1);
        $this->assertEquals($temp['offset'], $offset);
        $this->assertEquals($item, $items[0]);
    }
    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testGetByKey(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $filters = $this->makeFiltersByKey(...$items);

        $this->assertEquals($repository->getByKey(...$items), $items[0]);
        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['filters'], $filters);
        $this->assertEquals($temp['orders'], []);
        $this->assertEquals($temp['limit'], 1);
        $this->assertEquals($temp['offset'], 0);
    }

    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testFind(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $filters = [];
        $orders = [];
        $limit = 0;
        $offset = 0;

        $this->assertEquals($repository->find($filters, $orders, $limit, $offset), $items);
        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['filters'], $filters);
        $this->assertEquals($temp['orders'], $orders);
        $this->assertEquals($temp['limit'], $limit);
        $this->assertEquals($temp['offset'], $offset);
    }

    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testFindByKey(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $filters = $this->makeFiltersByKey(...$items);

        $this->assertEquals($repository->findByKey(...$items), $items);
        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['filters'], $filters);
        $this->assertEquals($temp['orders'], []);
        $this->assertEquals($temp['limit'], 0);
        $this->assertEquals($temp['offset'], 0);
    }

    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testCount(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $filters = [];

        $this->assertEquals($repository->count($filters), count($items));
        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['filters'], $filters);
    }

    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testCountByKey(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $filters = $this->makeFiltersByKey(...$items);

        $this->assertEquals($repository->countByKey(...$items), count($items));
        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['filters'], $filters);
    }

    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testInsert(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $repository->insert(...$items);
        $this->assertEquals($temp['rows'], $items);
        $this->assertEquals($temp['metadata'], $metadata);
    }

    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testUpdate(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $filters = [];
        $orders = [];
        $limit = 0;
        $offset = 0;

        $repository->update($items[0], $filters, $orders, $limit, $offset);
        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['values'], $items[0]);
        $this->assertEquals($temp['filters'], $filters);
        $this->assertEquals($temp['orders'], $orders);
        $this->assertEquals($temp['limit'], $limit);
        $this->assertEquals($temp['offset'], $offset);
    }

    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testUpdateByKey(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $filters = $this->makeFiltersByKey(...$items);

        $repository->updateByKey($items[0], ...$items);
        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['values'], $items[0]);
        $this->assertEquals($temp['filters'], $filters);
        $this->assertEquals($temp['orders'], []);
        $this->assertEquals($temp['limit'], 0);
        $this->assertEquals($temp['offset'], 0);
    }

    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testDelete(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $filters = [];
        $orders = [];
        $limit = 0;
        $offset = 0;

        $repository->delete($filters, $orders, $limit, $offset);
        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['filters'], $filters);
        $this->assertEquals($temp['orders'], $orders);
        $this->assertEquals($temp['limit'], $limit);
        $this->assertEquals($temp['offset'], $offset);
    }

    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testDeleteByKey(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $filters = $this->makeFiltersByKey(...$items);

        $repository->deleteByKey(...$items);
        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['filters'], $filters);
        $this->assertEquals($temp['orders'], []);
        $this->assertEquals($temp['limit'], 0);
        $this->assertEquals($temp['offset'], 0);
    }

    /**
     * @param array $items
     * @dataProvider itemsProvider
     */
    public function testLock(array $items)
    {
        $temp = [];
        $driver = $this->makeMockDriver($temp, $items);

        $metadata = new Metadata('test', []);
        $repository = new Repository($driver, $metadata);

        $wait = false;
        $repository->lock($locks, $wait, $items[0]);

        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['resource'], json_encode($items[0]));
        $this->assertEquals($temp['wait'], $wait);
        $this->assertEquals(count($locks), 1);

        $e = null;
        try {
            $repository->lock($locks, $wait, $items[1]);
        } catch (CannotLockException $e) {}

        $this->assertInstanceOf(CannotLockException::class, $e);

        $this->assertEquals($temp['metadata'], $metadata);
        $this->assertEquals($temp['resource'], json_encode($items[1]));
        $this->assertEquals($temp['wait'], $wait);

        $this->assertEquals(count($locks), 1);

        $this->assertEquals($locks, [json_encode($items[0])]);
    }

    public function itemsProvider(): array
    {
        return [
            [[['a' => 1], ['a' => 2]]],
        ];
    }

    private function makeMockDriver(&$temp, array &$items): DriverInterface
    {
        return new class($temp, $items) extends NullDriver
        {
            public function find(MetadataInterface $metadata, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array
            {
                $this->temp = [
                    'metadata' => $metadata,
                    'filters' => $filters,
                    'orders' => $orders,
                    'limit' => $limit,
                    'offset' => $offset,
                ];

                return $this->items;
            }

            public function count(MetadataInterface $metadata, array $filters = []): int
            {
                $this->temp = [
                    'metadata' => $metadata,
                    'filters' => $filters,
                ];

                return count($this->items);
            }

            public function insert(MetadataInterface $metadata, array &$rows): void
            {
                $this->temp = [
                    'metadata' => $metadata,
                    'rows' => $rows,
                ];
            }

            public function update(MetadataInterface $metadata, array $values, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
            {
                $this->temp = [
                    'metadata' => $metadata,
                    'values' => $values,
                    'filters' => $filters,
                    'orders' => $orders,
                    'limit' => $limit,
                    'offset' => $offset,
                ];
            }

            public function delete(MetadataInterface $metadata, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
            {
                $this->temp = [
                    'metadata' => $metadata,
                    'filters' => $filters,
                    'orders' => $orders,
                    'limit' => $limit,
                    'offset' => $offset,
                ];
            }

            public function lock(MetadataInterface $metadata, string $resource, bool $wait = true): bool
            {
                $this->temp = [
                    'metadata' => $metadata,
                    'resource' => $resource,
                    'wait' => $wait,
                ];

                return json_decode($resource, true) == $this->items[0];
            }

            public function unlock(MetadataInterface $metadata, string $resource): bool
            {
                $this->temp = [
                    'metadata' => $metadata,
                    'resource' => $resource,
                ];

                return json_decode($resource, true) == $this->items[0];
            }
        };
    }

    private function makeFiltersByKey(...$items)
    {
        $filters = [];
        foreach ($items as $item) {
            $keyFilters = [];
            foreach ($item as $k => $v) {
                $keyFilters[] = [FilterInterface::EQ, $k, $v];
            }
            $filters[] = [FilterInterface::AND, $keyFilters];
        }
        return [
            [FilterInterface::OR, $filters],
        ];
    }
}