<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

use Ueef\Packable\Interfaces\PackableInterface;

interface EntityInterface extends PackableInterface
{
    /**
     * @param array $packed
     * @return EntityInterface
     */
    public static function unpack(array $packed);
}