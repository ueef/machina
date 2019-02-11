<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

use Ueef\Packable\Interfaces\PackableInterface;

interface EntityInterface extends PackableInterface
{
    public function getEntityId(): array;
}