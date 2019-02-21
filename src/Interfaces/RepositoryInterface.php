<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface RepositoryInterface
{
    public function get(array $filters = [], array $orders = [], int $offset = 0): ?array;
    public function getById(array $id): ?array;

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array;
    public function findByIds(array $ids): array;

    public function count(array $filters = []): int;
    public function countByIds(array $ids): int;

    public function insert(array &$items): void;

    public function update(array $values, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void;
    public function updateByIds(array $values, array $ids): void;

    public function delete(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void;
    public function deleteByIds(array $ids): void;

    public function lock(string $resource, ?array &$locks, bool $wait = true): bool;
    public function lockById(array $id, ?array &$locks, bool $wait = true): bool;
    public function unlock(array $locks): void;

    public function getItemId(array $item): array;

    public function getMetadata(): MetadataInterface;
}