<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface LockableDriverInterface
{
    public function lock(MetadataInterface $metadata, string $resource, bool $wait = true): bool;
    public function unlock(MetadataInterface $metadata, string $resource): bool;
}