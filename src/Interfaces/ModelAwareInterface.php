<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface ModelAwareInterface
{
    /**
     * @return ModelInterface
     */
    public static function model();

    public static function setModel(ModelInterface $model);
}