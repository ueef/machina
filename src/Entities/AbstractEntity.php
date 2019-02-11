<?php
declare(strict_types=1);

namespace Ueef\Machina\Entities;

use Ueef\Machina\Exceptions\EntityException;
use Ueef\Machina\Interfaces\ModelInterface;
use Ueef\Machina\Interfaces\EntityInterface;
use Ueef\Machina\Interfaces\ModelAwareInterface;

abstract class AbstractEntity implements EntityInterface, ModelAwareInterface
{
    /** @var ModelInterface[] */
    private static $models;

    public static function model()
    {
        if (static::$models[static::class]) {
            return static::$models[static::class];
        }

        throw new EntityException(["there isn't a model for %s", static::class]);
    }

    public static function setModel(ModelInterface $model)
    {
        static::$models[static::class] = $model;
    }
}