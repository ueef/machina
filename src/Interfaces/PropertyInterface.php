<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface PropertyInterface
{
    const TYPE_INT = 'integer';
    const TYPE_STR = 'string';
    const TYPE_BOOL = 'boolean';
    const TYPE_FLOAT = 'double';
    const TYPE_ARRAY = 'array';
    const TYPE_NUMERIC = 'numeric';

    public function getType(): string;
    public function isGenerated(): bool;
}