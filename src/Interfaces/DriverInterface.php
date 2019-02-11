<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface DriverInterface
{
    public function find(MetadataInterface $metadata, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array;
    public function count(MetadataInterface $metadata, array $filters = []): int;
    public function insert(MetadataInterface $metadata, array &$rows): void;
    public function update(MetadataInterface $metadata, array $values, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void;
    public function delete(MetadataInterface $metadata, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void;
}