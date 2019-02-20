<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

use Ueef\Machina\Exceptions\PropertyValidationException;

class IntProperty extends AbstractProperty
{
    public function getType(): string
    {
        return self::TYPE_INT;
    }

    public function validate($value): void
    {
        if (!is_integer($value)) {
            throw new PropertyValidationException(["value must be the type of integer, %s given", gettype($value)]);
        }
    }

    public function getStubValue()
    {
        return 0;
    }
}