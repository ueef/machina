<?php
declare(strict_types=1);

namespace Ueef\Machina;

use Ueef\Machina\Exceptions\RepositoryException;
use Ueef\Machina\Interfaces\DriverInterface;
use Ueef\Machina\Interfaces\FilterInterface;
use Ueef\Machina\Interfaces\MetadataInterface;
use Ueef\Machina\Interfaces\RepositoryInterface;
use Ueef\Machina\Interfaces\LockableDriverInterface;
use Ueef\Machina\Interfaces\TransactionalDriverInterface;

class Repository implements RepositoryInterface
{
    /** @var DriverInterface */
    private $driver;

    /** @var MetadataInterface */
    private $metadata;

    /** @var array */
    private $proto_id;


    public function __construct(DriverInterface $driver, MetadataInterface $metadata)
    {
        $this->driver = $driver;
        $this->metadata = $metadata;
        $this->proto_id = $this->getItemId([]);
    }

    public function get(array $filters = [], array $orders = [], int $offset = 0): ?array
    {
        $items = $this->find($filters, $orders, 1, $offset);
        if ($items) {
            return $items[0];
        }

        return null;
    }

    public function getById(array $id): ?array
    {
        return $this->get($this->getFiltersById($id));
    }

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array
    {
        return $this->driver->find($this->metadata, $filters, $orders, $limit, $offset);
    }

    public function findByIds(array $ids): array
    {
        $items = [];
        foreach ($ids as $index => $id) {
            $ids[$index] = $this->correctId($id);
            $items[$index] = null;
        }

        foreach ($this->find($this->getFiltersByIds($ids)) as $item) {
            $index = array_search($this->getItemId($item), $ids);
            if (false === $index) {
                throw new RepositoryException("cannot match a founded item by id");
            }
            $items[$index] = $item;
        }

        return $items;
    }

    public function count(array $filters = []): int
    {
        return $this->driver->count($this->metadata, $filters);
    }

    public function countByIds(array $ids): int
    {
        return $this->driver->count($this->metadata, $this->getFiltersByIds($ids));
    }

    public function insert(array &$items): void
    {
        $this->driver->insert($this->metadata, $items);
    }

    public function update(array $values, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
    {
        $this->driver->update($this->metadata, $values, $filters, $orders, $limit, $offset);
    }

    public function updateByIds(array $values, array $ids): void
    {
        $this->update($values, $this->getFiltersByIds($ids));
    }

    public function delete(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
    {
        $this->driver->delete($this->metadata, $filters, $orders, $limit, $offset);
    }

    public function deleteByIds(array $ids): void
    {
        $this->delete($this->getFiltersByIds($ids));
    }

    public function lock(string $resource, bool $wait = true): bool
    {
        if ($this->driver instanceof LockableDriverInterface) {
            return $this->driver->lock($this->metadata, $resource, $wait);
        } else {
            throw new RepositoryException(["%s doesn't implement %s", get_class($this->driver), LockableDriverInterface::class]);
        }
    }

    public function lockById(array $id, bool $wait = true): bool
    {
        return $this->lock($this->getLocking($id), $wait);
    }

    public function unlock(string $resource): bool
    {
        if ($this->driver instanceof LockableDriverInterface) {
            return $this->driver->unlock($this->metadata, $resource);
        } else {
            throw new RepositoryException(["%s doesn't implement %s", get_class($this->driver), LockableDriverInterface::class]);
        }
    }

    public function unlockById(array $id): bool
    {
        return $this->unlock($this->getLocking($id));
    }

    public function begin(): void
    {
        if ($this->driver instanceof TransactionalDriverInterface) {
            $this->driver->begin();
        } else {
            throw new RepositoryException(["%s doesn't implement %s", get_class($this->driver), TransactionalDriverInterface::class]);
        }
    }

    public function commit(): void
    {
        if ($this->driver instanceof TransactionalDriverInterface) {
            $this->driver->commit();
        } else {
            throw new RepositoryException(["%s doesn't implement %s", get_class($this->driver), TransactionalDriverInterface::class]);
        }
    }

    public function rollback(): void
    {
        if ($this->driver instanceof TransactionalDriverInterface) {
            $this->driver->rollback();
        } else {
            throw new RepositoryException(["%s doesn't implement %s", get_class($this->driver), TransactionalDriverInterface::class]);
        }
    }

    public function getItemId(array $item): array
    {
        $id = [];
        foreach ($this->metadata->getIdentifiedProperties() as $key => $property) {
            $id[$key] = $item[$key] ?? null;
        }

        return $id;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    private function correctId(array $id): array
    {
        return array_replace($this->proto_id, array_intersect_key($id, $this->proto_id));
    }

    private function getLocking(array $id): string
    {
        return json_encode($this->correctId($id));
    }

    private function getFiltersById(array $id)
    {
        $filters = [];
        foreach ($id as $key => $value) {
            $filters[] = [FilterInterface::EQ, $key, $value];
        }

        return $filters;
    }

    private function getFiltersByIds(array $ids)
    {
        $filters = [];
        foreach ($ids as $id) {
            $filters[] = [FilterInterface::AND, $this->getFiltersById($id)];
        }

        return [
            [FilterInterface::OR, $filters],
        ];
    }
}