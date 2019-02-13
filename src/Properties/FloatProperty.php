<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

class FloatProperty extends AbstractProperty
{
    public function getType(): string
    {
        return self::TYPE_FLOAT;
    }

    public function getStubValue()
    {
        return 0.0;
    }
}