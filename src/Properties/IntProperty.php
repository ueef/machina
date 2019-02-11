<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

class IntProperty extends AbstractProperty
{
    public function getType(): string
    {
        return self::TYPE_INT;
    }
}