<?php
declare(strict_types=1);

namespace Ueef\Machina\Managers;

use Ueef\Packer\Interfaces\PackerInterface;
use Ueef\Machina\Interfaces\ManagerInterface;
use Ueef\Machina\Exceptions\ManagerException;

abstract class AbstractManager implements ManagerInterface, PackerInterface
{
    public function has(object ...$entities): bool
    {
        return $this->hasByKey(...$this->extractPrimaryKeys($entities));
    }

    public function hasByKey(array ...$keys): bool
    {
        return count($keys) == $this->countByKey(...$keys);
    }

    public function get(array $filters = [], array $orders = [], int $offset = 0)
    {
        $item = $this->getRepository()->get($filters, $orders, $offset);
        if ($item) {
            return $this->unpack($item);
        }

        return null;
    }

    public function getByKey(array ...$keys)
    {
        $item = $this->getRepository()->getByKey(...$keys);
        if ($item) {
            return $this->unpack($item);
        }

        return null;
    }

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array
    {
        return $this->unpackMany($this->getRepository()->find($filters, $orders, $limit, $offset));
    }

    public function findByKey(array ...$keys): array
    {
        return $this->unpackMany($this->getRepository()->findByKey(...$keys));
    }

    public function count(array $filters = []): int
    {
        return $this->getRepository()->count($filters);
    }

    public function countByKey(array ...$keys): int
    {
        return $this->getRepository()->countByKey(...$keys);
    }

    public function reload(object &...$entities): void
    {
        if (count($entities) != $this->refresh(...$entities)) {
            throw new ManagerException("cannot find some of the entities");
        }
    }

    public function refresh(object &...$entities): bool
    {
        $keys = $this->extractPrimaryKeys($entities);

        $n = 0;
        foreach ($this->findByKey(...$keys) as $entity) {
            $index = array_search($this->extractPrimaryKey($entity), $keys);
            if (false !== $index) {
                $entities[$index] = $entity;
                $n++;
                unset($keys[$index]);
            }
        }

        return count($entities) == $n;
    }

    public function insert(object ...$entities): void
    {
        $items = $this->packMany($entities);
        $this->getRepository()->insert(...$items);
    }

    public function create(object &...$entities): void
    {
        $items = $this->packMany($entities);
        $this->getRepository()->insert(...$items);

        foreach ($this->unpackMany($items) as $i => $entity) {
            $entities[$i] = $entity;
        }

        if (!$this->refresh(...$entities)) {
            throw new ManagerException("cannot find some entities after insert");
        }
    }

    public function update(object &...$entities): void
    {
        foreach ($entities as $entity) {
            $this->updateByKey($this->pack($entity), $this->extractPrimaryKey($entity));
        }

        if (!$this->refresh(...$entities)) {
            throw new ManagerException("cannot find some entities after update");
        }
    }

    public function updateByKey(array $values, array ...$keys): void
    {
        $this->getRepository()->updateByKey($values, ...$keys);
    }

    public function delete(object ...$entities): void
    {
        $this->deleteByKey(...$this->extractPrimaryKeys($entities));
    }

    public function deleteByKey(array ...$keys): void
    {
        $this->getRepository()->deleteByKey(...$keys);
    }

    public function lock(?array &$locks, bool $wait, object ...$entities): void
    {
        $this->getRepository()->lock($locks, $wait, $this->extractPrimaryKeys($entities));
    }

    public function lockByKey(?array &$locks, bool $wait, array ...$keys): void
    {
        $this->getRepository()->lock($locks, $wait, $keys);
    }

    public function unlock(array &$locks): void
    {
        $this->getRepository()->unlock($locks);
    }

    public function begin(): void
    {
        $this->getRepository()->begin();
    }

    public function commit(): void
    {
        $this->getRepository()->commit();
    }

    public function rollback(): void
    {
        $this->getRepository()->rollback();
    }

    private function packMany(array $entities): array
    {
        $items = [];
        foreach ($entities as $i => $entity) {
            if (null === $entity) {
                $items[$i] = null;
            } else {
                $items[$i] = $this->pack($entity);
            }
        }

        return $items;
    }

    private function unpackMany(array $items): array
    {
        $entities = [];
        foreach ($items as $i => $item) {
            if (null === $item) {
                $entities[$i] = null;
            } else {
                $entities[$i] = $this->unpack($item);
            }
        }

        return $entities;
    }

    protected function extractPrimaryKeys(array $entities): array
    {
        $keys = [];
        foreach ($entities as $i => $entity) {
            $keys[$i] = $this->extractPrimaryKey($entity);
        }

        return $keys;
    }

    abstract protected function extractPrimaryKey(object $entity): array;
}