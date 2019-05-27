<?php
declare(strict_types=1);

namespace Ueef\Machina\Managers;

use Ueef\Machina\Interfaces\ManagerInterface;
use Ueef\Machina\Exceptions\EntitiesManagerException;

abstract class AbstractManager implements ManagerInterface
{
    public function has(object ...$entities): bool
    {
        $keys = [];
        foreach ($entities as $entity) {
            $keys[] = $this->extractPrimaryKey($entity);
        }

        return $this->hasByKey(...$keys);
    }

    public function hasByKey(array ...$keys): bool
    {
        return count($keys) == $this->countByKey($keys);
    }

    public function get(array $filters = [], array $orders = [], int $offset = 0)
    {
        $item = $this->getRepository()->get($filters, $orders, $offset);
        $entity = $this->unpack($item)[0];

        return $entity;
    }

    public function getByKey(array $key)
    {
        $item = $this->getRepository()->getByKey($key);
        $entities = $this->unpack($item);

        return $entities[0];
    }

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array
    {
        $items = $this->getRepository()->find($filters, $orders, $limit, $offset);
        $entities = $this->unpack(...$items);

        return $entities;
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
        return $this->getRepository()->countByKey($keys);
    }

    public function reload(object &...$entities): bool
    {
        return count($entities) == $this->refresh($entities);
    }

    public function refresh(object &...$entities): int
    {
        $keys = [];
        foreach ($entities as $i => $entity) {
            $keys[$i] = $this->extractPrimaryKey($entity);
        }

        $n = 0;
        foreach ($this->findByKey($keys) as $i => $entity) {
            if (null === $entity) {
                continue;
            }

            $entities[$i] = $entity;
            $n++;
        }

        return $n;
    }

    public function insert(object ...$entities): void
    {
        $items = $this->packMany($entities);
        $this->getRepository()->insert($items);
    }

    public function create(object &...$entities): void
    {
        $items = $this->packMany($entities);
        $this->getRepository()->insert($items);

        $entities = $this->unpackMany($items);
        if (!$this->reload($entities)) {
            throw new EntitiesManagerException("cannot find some entities after insert");
        }
    }

    public function update(object &...$entities): void
    {
        foreach ($entities as &$entity) {
            $this->getRepository()->updateByKey($this->pack($entity), $this->extractPrimaryKey($entity));
        }

        if (!$this->reload($entities)) {
            throw new EntitiesManagerException("cannot find some entities after update");
        }
    }

    public function updateByKey(array $values, array ...$keys): void
    {
        $this->getRepository()->updateByKey($values, ...$keys);
    }

    public function delete(object ...$entities): void
    {
        $keys = [];
        foreach ($entities as $index => $entity) {
            $keys[$index] = $this->extractPrimaryKey($entity);
        }

        $this->deleteByKey(...$keys);
    }

    public function deleteByKey(array ...$keys): void
    {
        $this->getRepository()->deleteByKey($keys);
    }

    public function lock(object $entity, array &$locks, bool $wait = true): void
    {
        $this->getRepository()->lock($this->extractPrimaryKey($entity), $locks, $wait);
    }

    public function lockByKey(array $key, array &$locks, bool $wait = true): void
    {
        $this->getRepository()->lock($key, $locks, $wait);
    }

    public function unlock(array $locks): void
    {
        $this->getRepository()->unlock($locks);
    }

    private function packMany(array $entities): array
    {
        $items = [];
        foreach ($entities as $i => $entity) {
            $items[$i] = $this->pack($entity);
        }

        return $items;
    }

    private function unpackMany(array $items): array
    {
        $entities = [];
        foreach ($items as $i => $item) {
            $entities[$i] = $this->unpack($item);
        }

        return $entities;
    }

    abstract protected function pack(object $entity): array;
    abstract protected function unpack(?array $item);
    abstract protected function extractPrimaryKey(object $entity): array;
}