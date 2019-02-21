<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface EntitiesManagerInterface
{
    public function get(array $filters = [], array $orders = [], int $offset = 0);
    public function getById(array $id);

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array;
    public function findByIds(array $ids): array;

    public function count(array $filters = []): int;
    public function countByIds(array $ids): int;

    public function insert(EntityInterface ...$entities): void;
    public function create(EntityInterface &...$entities): void;

    public function update(EntityInterface &...$entities): void;
    public function updateByIds(array $values, array $ids): void;

    public function delete(EntityInterface &...$entities): void;
    public function deleteByIds(array $ids): void;

    public function has(EntityInterface ...$entities): bool;
    public function reload(EntityInterface &...$entities): void;
    public function refresh(EntityInterface &...$entities): array;

    public function lock(EntityInterface $entity, ?array &$locks, bool $wait = true): void;
    public function lockById(array $id, ?array &$locks, bool $wait = true): void;
    public function lockByKey(array $key, ?array &$locks, bool $wait = true): void;
    public function unlock(array $locks): void;

    public function getEntityId(EntityInterface $entity): array;
    public function getRepository(): RepositoryInterface;
}