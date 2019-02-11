<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

class NumericProperty extends AbstractProperty
{
    public function getType(): string
    {
        return self::TYPE_NUMERIC;
    }
}