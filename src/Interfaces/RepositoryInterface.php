<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface RepositoryInterface {

    public function get(array $filters = [], array $orders = [], int $offset = 0): ?array;
    public function getByKey(array ...$keys): ?array;

    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array;
    public function findByKey(array ...$keys): array;

    public function count(array $filters = []): int;
    public function countByKey(array ...$keys): int;

    public function insert(array &...$items): void;

    public function update(array $values, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void;
    public function updateByKey(array $values, array ...$keys): void;

    public function delete(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void;
    public function deleteByKey(array ...$keys): void;

    public function lock(?array &$locks, bool $wait, array ...$keys): void;
    public function unlock(array &$locks): void;

    public function getDriver(): DriverInterface;
    public function getMetadata(): MetadataInterface;
}