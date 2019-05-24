<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface ManagerInterface
{
    /**
     * @param object ...$entities
     * @return bool
     */
    public function has(...$entities): bool;

    /**
     * @param array ...$keys
     * @return bool
     */
    public function hasByKey(array ...$keys): bool;

    /**
     * @param array $filters
     * @param array $orders
     * @param int $offset
     * @return object
     */
    public function get(array $filters = [], array $orders = [], int $offset = 0);

    /**
     * @param array $key
     * @return mixed
     */
    public function getByKey(array $key);

    /**
     * @param array $filters
     * @param array $orders
     * @param int $limit
     * @param int $offset
     * @return object[]
     */
    public function find(array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array;

    /**
     * @param array ...$keys
     * @return object[]
     */
    public function findByKey(array ...$keys): array;

    /**
     * @param array $filters
     * @return int
     */
    public function count(array $filters = []): int;

    /**
     * @param array ...$keys
     * @return int
     */
    public function countByKey(array ...$keys): int;

    /**
     * @param object ...$entities
     * @return bool
     */
    public function reload(&...$entities): bool;

    /**
     * @param object ...$entities
     * @return int
     */
    public function refresh(&...$entities): int;

    /**
     * @param object ...$entities
     */
    public function insert(...$entities): void;

    /**
     * @param object ...$entities
     */
    public function create(&...$entities): void;

    /**
     * @param object ...$entities
     */
    public function update(&...$entities): void;

    /**
     * @param array $values
     * @param array ...$keys
     */
    public function updateByKey(array $values, array ...$keys): void;

    /**
     * @param object ...$entities
     */
    public function delete(...$entities): void;

    /**
     * @param array ...$keys
     */
    public function deleteByKey(array ...$keys): void;

    /**
     * @param $entity
     * @param array $locks
     * @param bool $wait
     */
    public function lock($entity, array &$locks, bool $wait = true): void;

    /**
     * @param array $key
     * @param array $locks
     * @param bool $wait
     */
    public function lockByKey(array $key, array &$locks, bool $wait = true): void;

    /**
     * @param array $locks
     */
    public function unlock(array $locks): void;

    /**
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;
}