<?php
declare(strict_types=1);

namespace Ueef\Machina;

use Ueef\Machina\Interfaces\EntityInterface;
use Ueef\Packable\Interfaces\PackableInterface;

class Entity implements EntityInterface
{
    public function getEntityId(): array
    {
        return [];
    }

    public function pack(): array
    {
        return [];
    }

    public static function unpack(array $packed): PackableInterface
    {
        return new self;
    }
}