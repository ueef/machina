<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

use Ueef\Machina\Exceptions\PropertyValidationException;

class ArrayProperty extends AbstractProperty
{
    public function getType(): string
    {
        return self::TYPE_ARRAY;
    }

    public function validate($value): void
    {
        if (!is_array($value)) {
            throw new PropertyValidationException(["value must be the type of array, %s given", gettype($value)]);
        }
    }

    public function getStubValue()
    {
        return [];
    }
}