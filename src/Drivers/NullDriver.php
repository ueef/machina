<?php
declare(strict_types=1);

namespace Ueef\Machina\Drivers;

use Ueef\Machina\Exceptions\DriverException;
use Ueef\Machina\Interfaces\DriverInterface;
use Ueef\Machina\Interfaces\FilterInterface;
use Ueef\Machina\Interfaces\MetadataInterface;
use Ueef\Machina\Interfaces\PropertyInterface;

class NullDriver implements DriverInterface
{
    /** @var mixed */
    protected $temp;

    /** @var array[] */
    protected $items;


    public function __construct(&$temp, array $items)
    {
        $this->temp = &$temp;
        $this->items = $items;
    }

    public function find(MetadataInterface $metadata, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): array
    {
        return [];
    }

    public function count(MetadataInterface $metadata, array $filters = []): int
    {
        return 0;
    }

    public function insert(MetadataInterface $metadata, array &$rows): void
    {
        return;
    }

    public function update(MetadataInterface $metadata, array $values, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
    {
        return;
    }

    public function delete(MetadataInterface $metadata, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
    {
        return;
    }

    public function lock(MetadataInterface $metadata, string $resource, bool $wait = true): bool
    {
        return true;
    }

    public function unlock(MetadataInterface $metadata, string $resource): bool
    {
        return true;
    }

    public function begin(): void
    {
        return;
    }

    public function commit(): void
    {
        return;
    }

    public function rollback(): void
    {
        return;
    }
}