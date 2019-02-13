<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

class BoolProperty extends AbstractProperty
{
    public function getType(): string
    {
        return self::TYPE_BOOL;
    }

    public function getStubValue()
    {
        return false;
    }
}