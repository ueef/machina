<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

use Ueef\Packable\Interfaces\PackableInterface;

interface EntityInterface extends PackableInterface
{
    /**
     * @return EntityInterface
     */
    public function unpack(array $values);
}