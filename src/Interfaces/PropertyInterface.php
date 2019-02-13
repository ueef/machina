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

    public function getType(): string;
    public function validate($value): void;
    public function isGenerated(): bool;
    public function isIdentified(): bool;
}