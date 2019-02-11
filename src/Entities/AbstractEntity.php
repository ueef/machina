<?php
declare(strict_types=1);

namespace Ueef\Machina\Entities;

use Ueef\Machina\Interfaces\EntityInterface;
use Ueef\Machina\Interfaces\ModelAwareInterface;
use Ueef\Machina\Interfaces\ModelInterface;

abstract class AbstractEntity implements EntityInterface, ModelAwareInterface
{
    /** @var ModelInterface[] */
    private static $models;

    public static function getModel()
    {
        return static::$models[static::class];
    }

    public static function setModel(ModelInterface $model)
    {
        static::$models[static::class] = $model;
    }
}