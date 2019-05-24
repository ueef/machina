<?php
declare(strict_types=1);

namespace Ueef\Machina;

use Ueef\Machina\Exceptions\CannotLockException;
use Ueef\Machina\Interfaces\DriverInterface;
use Ueef\Machina\Interfaces\FilterInterface;
use Ueef\Machina\Interfaces\MetadataInterface;
use Ueef\Machina\Interfaces\RepositoryInterface;

class Repository implements RepositoryInterface
{
    /** @var DriverInterface */
    private $driver;

    /** @var MetadataInterface */
    private $metadata;


    public function __construct(DriverInterface $driver, MetadataInterface $metadata)
    {
        $this->driver = $driver;
        $this->metadata = $metadata;
    }

    public function get(array $filters = [], array $orders = [], int $offset = 0): ?array
    {
        $items = $this->find($filters, $orders, 1, $offset);
        if ($items) {
            return $items[0];
        }

        return null;
    }

    public function getByKey(array $key): ?array
    {
        $items = $this->get($this->makeFiltersByKey($key));
        if ($items) {
            return $items[0];
        }

        return null;
    }

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array
    {
        return $this->driver->find($this->metadata, $filters, $orders, $limit, $offset);
    }

    public function findByKey(array ...$keys): array
    {
        return $this->combineKeysWithItems($this->find($this->makeFiltersByKey(...$keys)), $keys);
    }

    public function count(array $filters = []): int
    {
        return $this->driver->count($this->metadata, $filters);
    }

    public function countByKey(array ...$keys): int
    {
        return $this->driver->count($this->metadata, $this->makeFiltersByKey(...$keys));
    }

    public function insert(array &$items): void
    {
        $this->driver->insert($this->metadata, $items);
    }

    public function update(array $values, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
    {
        $this->driver->update($this->metadata, $values, $filters, $orders, $limit, $offset);
    }

    public function updateByKey(array $values, array ...$keys): void
    {
        $this->update($values, $this->makeFiltersByKey(...$keys));
    }

    public function delete(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
    {
        $this->driver->delete($this->metadata, $filters, $orders, $limit, $offset);
    }

    public function deleteByKey(array ...$keys): void
    {
        $this->delete($this->makeFiltersByKey(...$keys));
    }

    public function lock(array $key, array &$locks, bool $wait = true): void
    {
        $resource = json_encode($key);
        if ($this->driver->lock($this->metadata, $resource, $wait)) {
            $locks[] = $resource;
        } else {
            throw new CannotLockException(["cannot lock item by key: %s", $key]);
        }
    }

    public function unlock(array $locks): void
    {
        foreach ($locks as $resource) {
            $this->driver->unlock($this->metadata, $resource);
        }
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    private function compareKeyWithItem(array $key, array $item): bool
    {
        foreach ($key as $k => $v) {
            if (!isset($item[$k]) || $key[$k] !== $item[$k]) {
                return false;
            }
        }

        return true;
    }

    private function combineKeysWithItems(array $items, array $keys): array
    {
        $r = [];
        foreach ($keys as $i => $key) {
            $r[$i] = null;
            foreach ($items as $_i => $item) {
                if ($this->compareKeyWithItem($key, $item)) {
                    $r[$i] = $item;
                }
            }
        }

        return $r;
    }

    private function makeFiltersByKey(array ...$keys): array
    {
        $filters = [];
        foreach ($keys as $key) {
            $keyFilters = [];
            foreach ($key as $index => $value) {
                $keyFilters[] = [FilterInterface::EQ, $index, $value];
            }
            $filters[] = [FilterInterface::AND, $keyFilters];
        }

        return [
            [FilterInterface::OR, $filters],
        ];
    }
}