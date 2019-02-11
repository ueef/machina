<?php
declare(strict_types=1);

namespace Ueef\Machina;

use Throwable;
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


    public function __construct(DriverInterface $driver, MetadataInterface $metadata)
    {
        $this->driver = $driver;
        $this->metadata = $metadata;
    }

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array
    {
        return $this->driver->find($this->metadata, $filters, $orders, $limit, $offset);
    }

    public function count(array $filters = []): int
    {
        return $this->driver->count($this->metadata, $filters);
    }

    public function insert(array &$items): void
    {
        $this->driver->insert($this->metadata, $items);
    }

    public function update(array $values, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
    {
        $this->driver->update($this->metadata, $values, $filters, $orders, $limit, $offset);
    }

    public function delete(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
    {
        $this->driver->delete($this->metadata, $filters, $orders, $limit, $offset);
    }

    public function lock(string $resource, bool $wait = true): bool
    {
        if ($this->driver instanceof LockableDriverInterface) {
            return $this->driver->lock($this->metadata, $resource, $wait);
        }

        throw new RepositoryException(["%s doesn't implement %s", get_class($this->driver), LockableDriverInterface::class]);
    }

    public function unlock(string $resource): bool
    {
        if ($this->driver instanceof LockableDriverInterface) {
            return $this->driver->unlock($this->metadata, $resource);
        }

        throw new RepositoryException(["%s doesn't implement %s", get_class($this->driver), LockableDriverInterface::class]);
    }

    public function begin(): void
    {
        if ($this->driver instanceof TransactionalDriverInterface) {
            $this->driver->begin();
        }

        throw new RepositoryException(["%s doesn't implement %s", get_class($this->driver), TransactionalDriverInterface::class]);
    }

    public function commit(): void
    {
        if ($this->driver instanceof TransactionalDriverInterface) {
            $this->driver->commit();
        }

        throw new RepositoryException(["%s doesn't implement %s", get_class($this->driver), TransactionalDriverInterface::class]);
    }

    public function rollback(): void
    {
        if ($this->driver instanceof TransactionalDriverInterface) {
            $this->driver->rollback();
        }

        throw new RepositoryException(["%s doesn't implement %s", get_class($this->driver), TransactionalDriverInterface::class]);
    }

    public function transact(callable $func): void
    {
        try {
            $this->begin();
            $func();
            $this->commit();
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
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
}