<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface RepositoryInterface
{
    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array;
    public function count(array $filters = []): int;
    public function insert(array &$items): void;
    public function update(array $values, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void;
    public function delete(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void;

    public function lock(string $resource, bool $wait = true): bool;
    public function unlock(string $resource): bool;

    public function begin(): void;
    public function commit(): void;
    public function rollback(): void;

    public function getItemId(array $item): array;

    public function getMetadata(): MetadataInterface;
}