<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface ModelInterface
{
    public function get(array $filters = [], array $orders = [], int $offset = 0);
    public function getById(array $id);

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array;
    public function findByIds(array $ids): array;

    public function count(array $filters = []): int;

    public function insert(EntityInterface ...$entities): void;
    public function create(EntityInterface &...$entities): void;
    public function update(EntityInterface &...$entities): void;
    public function delete(EntityInterface &...$entities): void;
    public function refresh(EntityInterface &...$entities): void;

    public function lock(EntityInterface $entity, bool $nowait = false): bool;
    public function unlock(EntityInterface $entity): bool;

    public function getRepository(): RepositoryInterface;
}