<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

use Ueef\Machina\Exceptions\PropertyValidationException;

class FloatProperty extends AbstractProperty
{
    public function getType(): string
    {
        return self::TYPE_FLOAT;
    }

    public function validate($value): void
    {
        if (!is_float($value)) {
            throw new PropertyValidationException(["value must be the type of double, %s given", gettype($value)]);
        }
    }

    public function getStubValue()
    {
        return 0.0;
    }
}