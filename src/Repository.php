<?php
declare(strict_types=1);

namespace Ueef\Machina;

use Ueef\Machina\Interfaces\DriverInterface;
use Ueef\Machina\Interfaces\FilterInterface;
use Ueef\Machina\Interfaces\MetadataInterface;
use Ueef\Machina\Interfaces\RepositoryInterface;
use Ueef\Machina\Exceptions\CannotLockException;
use Ueef\Machina\Exceptions\CannotUnlockException;

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

    public function getByKey(array ...$keys): ?array
    {
        return $this->get($this->makeFiltersByKey(...$keys));
    }

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array
    {
        return $this->driver->find($this->metadata, $filters, $orders, $limit, $offset);
    }

    public function findByKey(array ...$keys): array
    {
        return $this->find($this->makeFiltersByKey(...$keys));
    }

    public function count(array $filters = []): int
    {
        return $this->driver->count($this->metadata, $filters);
    }

    public function countByKey(array ...$keys): int
    {
        return $this->driver->count($this->metadata, $this->makeFiltersByKey(...$keys));
    }

    public function insert(array &...$items): void
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

    public function lock(?array &$locks, bool $wait, array ...$keys): void
    {
        if (null === $locks) {
            $locks = [];
        }

        $_locks = [];
        foreach ($keys as $key) {
            $resource = json_encode($key);
            if ($this->driver->lock($this->metadata, $resource, $wait)) {
                $_locks[] = $resource;
            } else {
                $this->unlock($_locks);
                throw new CannotLockException(["cannot lock item by key %s", $key]);
            }
        }

        foreach ($_locks as $resource) {
            $locks[] = $resource;
        }
    }

    public function unlock(array &$locks): void
    {
        while ($locks) {
            $lock = array_shift($locks);
            if (!$this->driver->unlock($this->metadata, $lock)) {
                array_unshift($locks, $lock);
                throw new CannotUnlockException(["cannot unlock resource: %s", $lock]);
            }
        }
    }

    public function begin(): void
    {
        $this->driver->begin();
    }

    public function commit(): void
    {
        $this->driver->commit();
    }

    public function rollback(): void
    {
        $this->driver->rollback();
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
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