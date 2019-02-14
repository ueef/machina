<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

use Ueef\Machina\Exceptions\PropertyValidationException;

class StrProperty extends AbstractProperty
{
    public function getType(): string
    {
        return self::TYPE_STR;
    }

    public function validate($value): void
    {
        if (!is_string($value)) {
            throw new PropertyValidationException(["value must be the type of string, %s given", gettype($value)]);
        }
    }

    public function getStubValue()
    {
        return '';
    }
}