<?php
declare(strict_types=1);

namespace Ueef\Machina;

use Ueef\Machina\Interfaces\EntityInterface;
use Ueef\Machina\Interfaces\RepositoryInterface;
use Ueef\Machina\Interfaces\EntitiesManagerInterface;
use Ueef\Machina\Exceptions\EntitiesManagerException;

class EntitiesManager implements EntitiesManagerInterface
{
    /** @var EntityInterface */
    private $proto;

    /** @var array */
    private $proto_id;

    /** @var RepositoryInterface */
    private $repository;


    public function __construct(EntityInterface $proto, RepositoryInterface $repository)
    {
        $this->proto = $proto;
        $this->repository = $repository;
        $this->proto_id = $this->getEntityId($proto);
    }


    public function get(array $filters = [], array $orders = [], int $offset = 0)
    {
        return $this->unpack($this->repository->get($filters, $orders, $offset));
    }

    public function getById(array $id)
    {
        return $this->unpack($this->repository->getById($id));
    }

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array
    {
        return $this->unpackMany($this->repository->find($filters, $orders, $limit, $offset));
    }

    public function findByIds(array $ids): array
    {
        return $this->unpackMany($this->repository->findByIds($ids));
    }

    public function count(array $filters = []): int
    {
        return $this->repository->count($filters);
    }

    public function countByIds(array $ids = []): int
    {
        return $this->repository->countByIds($ids);
    }

    public function insert(EntityInterface ...$entities): void
    {
        $items = $this->packMany($entities);
        $this->repository->insert($items);
    }

    public function create(EntityInterface &...$entities): void
    {
        $items = $this->packMany($entities);
        $this->repository->insert($items);

        $ids = [];
        foreach ($items as $index => $item) {
            $ids[$index] = $this->repository->getItemId($item);
        }

        foreach ($this->findByIds($ids) as $index => $entity) {
            if (null === $entity) {
                throw new EntitiesManagerException("cannot find one of inserted entities");
            }
            $entities[$index] = $entity;
        }
    }

    public function update(EntityInterface &...$entities): void
    {
        $ids = [];
        foreach ($this->packMany($entities) as $index => $item) {
            $ids[$index] = $this->repository->getItemId($item);
            $this->updateByIds($item, [$ids[$index]]);
        }

        foreach ($this->findByIds($ids) as $index => $entity) {
            if (null === $entity) {
                throw new EntitiesManagerException(["cannot find an entity after update: %s", $ids[$index]]);
            }
            $entities[$index] = $entity;
        }
    }

    public function updateByIds(array $values, array $ids): void
    {
        $this->repository->updateByIds($values, $ids);
    }

    public function delete(EntityInterface &...$entities): void
    {
        $ids = [];
        foreach ($entities as $index => $entity) {
            $ids[$index] = $this->getEntityId($entity);
        }

        $this->deleteByIds($ids);
    }

    public function deleteByIds(array $ids): void
    {
        $this->repository->deleteByIds($ids);
    }

    public function has(EntityInterface ...$entities): bool
    {
        $ids = [];
        foreach ($entities as $entity) {
            $ids[] = $this->getEntityId($entity);
        }

        return count($entities) == $this->countByIds($ids);
    }

    public function reload(EntityInterface &...$entities): void
    {
        $ids = [];
        foreach ($entities as $index => $entity) {
            $ids[$index] = $this->getEntityId($entity);
        }

        foreach ($this->findByIds($ids) as $index => $entity) {
            if (null === $entity) {
                throw new EntitiesManagerException("cannot find one of entities");
            }
            $entities[$index] = $entity;
        }
    }

    public function refresh(EntityInterface &...$entities): array
    {
        $ids = [];
        foreach ($entities as $index => $entity) {
            $ids[$index] = $this->getEntityId($entity);
        }

        $_entities = [];
        foreach ($this->findByIds($ids) as $index => $entity) {
            if ($entity) {
                $entities[$index] = $entity;
            }
            $_entities[$index] = $entity;
        }

        return $_entities;
    }

    public function lock(EntityInterface $entity, ?array &$locks, bool $wait = true): void
    {
        $this->lockById($this->getEntityId($entity), $locks, $wait);
    }

    public function lockById(array $id, ?array &$locks, bool $wait = true): void
    {
        if (!$this->repository->lockById($id, $locks, $wait)) {
            throw new EntitiesManagerException(["cannot lock entity: %s", $id]);
        }
    }

    public function unlock(array $locks): void
    {
        $this->repository->unlock($locks);
    }

    public function begin(): void
    {
        $this->repository->begin();
    }

    public function commit(): void
    {
        $this->repository->commit();
    }

    public function rollback(): void
    {
        $this->repository->rollback();
    }

    public function getEntityId(EntityInterface $entity): array
    {
        return $this->repository->getItemId($entity->pack());
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    private function pack(EntityInterface $entity): array
    {
        if ($entity instanceof $this->proto) {
            return $entity->pack();
        }

        throw new EntitiesManagerException(["item must be type of %s", get_class($this->proto)]);
    }

    private function unpack(?array $item)
    {
        if (null === $item) {
            return null;
        }

        return $this->proto->unpack($item);
    }

    private function packMany(array $entities): array
    {
        $items = [];
        foreach ($entities as $index => $entity) {
            $items[$index] = $this->pack($entity);
        }

        return $items;
    }

    private function unpackMany(array $items): array
    {
        $entities = [];
        foreach ($items as $index => $item) {
            $entities[$index] = $this->unpack($item);
        }

        return $entities;
    }
}