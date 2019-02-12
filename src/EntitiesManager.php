<?php
declare(strict_types=1);

namespace Ueef\Machina;

use Ueef\Machina\Exceptions\ModelException;
use Ueef\Machina\Interfaces\EntityInterface;
use Ueef\Machina\Interfaces\FilterInterface;
use Ueef\Machina\Interfaces\RepositoryInterface;
use Ueef\Machina\Interfaces\EntitiesManagerInterface;

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
        $items = $this->repository->find($filters, $orders, 1, $offset);
        $items = $this->unpack($items);
        if ($items) {
            return $items[0];
        }

        return null;
    }

    public function getById(array $id)
    {
        $items = $this->repository->find($this->makeFiltersById($this->correctId($id)), [], 1);
        $items = $this->unpack($items);
        if ($items) {
            return $items[0];
        }

        return null;
    }

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array
    {
        $items = $this->repository->find($filters, $orders, $limit, $offset);
        $items = $this->unpack($items);

        return $items;
    }

    public function findByIds(array $ids): array
    {
        $items = [];
        foreach ($ids as $index => $id) {
            $ids[$index] = $this->correctId($id);
            $items[$index] = null;
        }

        foreach ($this->repository->find($this->makeFiltersByIds($ids)) as $item) {
            $index = array_search($this->repository->getItemId($item), $ids);
            if (false === $index) {
                throw new ModelException("cannot match a founded item with id");
            }
            $items[$index] = $item;
        }

        return $this->unpack($items);
    }

    public function count(array $filters = []): int
    {
        return $this->repository->count($filters);
    }

    public function insert(EntityInterface ...$entities): void
    {
        $this->repository->insert($this->pack($entities));
    }

    public function create(EntityInterface &...$entities): void
    {
        $items = $this->pack($entities);
        $this->repository->insert($items);

        $ids = [];
        foreach ($items as $index => $item) {
            $ids[$index] = $this->repository->getItemId($item);
        }

        foreach ($this->findByIds($ids) as $index => $entity) {
            $entities[$index] = $entity;
        }
    }

    public function update(EntityInterface &...$entities): void
    {
        foreach ($this->pack($entities) as $index => $item) {
            $this->repository->update($item, $this->makeFiltersById($this->repository->getItemId($item)), [], 1);
        }

        $this->refresh($entities);
    }

    public function delete(EntityInterface &...$entities): void
    {
        $ids = [];
        foreach ($entities as $index => $entity) {
            $ids[$index] = $this->getEntityId($entity);
        }

        $this->repository->delete($this->makeFiltersByIds($ids));
    }

    public function refresh(EntityInterface &...$entities): void
    {
        $ids = [];
        foreach ($entities as $index => $entity) {
            $ids[$index] = $this->getEntityId($entity);
        }

        foreach ($this->findByIds($ids) as $index => $entity) {
            $entities[$index] = $entity;
        }
    }

    public function lock(EntityInterface $entity, bool $wait = true): bool
    {
        return $this->lockById($this->getEntityId($entity), $wait);
    }

    public function unlock(EntityInterface $entity): bool
    {
        return $this->unlockById($this->getEntityId($entity));
    }

    public function lockById(array $id, bool $wait = true): bool
    {
        return $this->repository->lock($this->makeLocking($id), $wait);
    }

    public function unlockById(array $id): bool
    {
        return $this->repository->unlock($this->makeLocking($id));
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

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @param EntityInterface[] $entities
     * @return array[]
     * @throws ModelException
     */
    private function pack(array $entities): array
    {
        foreach ($entities as &$entity) {
            if ($entity instanceof $this->proto) {
                $entity = $entity->pack();
            } else {
                throw new ModelException(["item must be type of %s", get_class($this->proto)]);
            }
        }

        return $entities;
    }

    /**
     * @param array[] $items
     * @return EntityInterface[]
     */
    private function unpack(array $items): array
    {
        foreach ($items as &$item) {
            if (null !== $item) {
                $item = $this->proto::unpack($item);
            }
        }

        return $items;
    }

    public function getEntityId(EntityInterface $entity): array
    {
        return $this->repository->getItemId($entity->pack());
    }

    private function makeLocking(array $id): string
    {
        return json_encode($this->correctId($id));
    }

    private function makeFiltersById(array $id)
    {
        $filters = [];
        foreach ($id as $key => $value) {
            $filters[] = [FilterInterface::EQ, $key, $value];
        }

        return $filters;
    }

    private function makeFiltersByIds(array $ids)
    {
        $filters = [];
        foreach ($ids as $id) {
            $filters[] = [FilterInterface::AND, $this->makeFiltersById($id)];
        }

        return [
            [FilterInterface::OR, $filters],
        ];
    }

    private function correctId(array $id): array
    {
        return array_replace($this->proto_id, array_intersect_key($id, $this->proto_id));
    }
}