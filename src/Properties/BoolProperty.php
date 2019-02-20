<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

use Ueef\Machina\Exceptions\PropertyValidationException;

class BoolProperty extends AbstractProperty
{
    public function getType(): string
    {
        return self::TYPE_BOOL;
    }

    public function validate($value): void
    {
        if (!is_bool($value)) {
            throw new PropertyValidationException(["value must be the type of boolean, %s given", gettype($value)]);
        }
    }

    public function getStubValue()
    {
        return false;
    }
}