<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface ManagerInterface
{
    public function has(object ...$entities): bool;
    public function hasByKey(array ...$keys): bool;

    public function get(array $filters = [], array $orders = [], int $offset = 0);
    public function getByKey(array ...$keys);

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): ObjectsCollectionInterface;
    public function findByKey(array ...$keys): ObjectsCollectionInterface;

    public function count(array $filters = []): int;
    public function countByKey(array ...$keys): int;

    public function reload(object &...$entities): void;
    public function refresh(object &...$entities): bool;

    public function insert(object ...$entities): void;
    public function create(object &...$entities): void;

    public function update(object &...$entities): void;
    public function updateByKey(array $values, array ...$keys): void;

    public function delete(object ...$entities): void;
    public function deleteByKey(array ...$keys): void;

    public function lock(?array &$locks, bool $wait, object ...$entities): void;
    public function lockByKey(?array &$locks, bool $wait, array ...$keys): void;
    public function unlock(array &$locks): void;

    public function begin(): void;
    public function commit(): void;
    public function rollback(): void;

    public function getRepository(): RepositoryInterface;
}