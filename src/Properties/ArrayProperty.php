<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

use Ueef\Machina\Errors\PropertyValidationError;

class ArrayProperty extends AbstractProperty
{
    public function getType(): string
    {
        return self::TYPE_ARRAY;
    }

    public function getStubValue()
    {
        return [];
    }
}