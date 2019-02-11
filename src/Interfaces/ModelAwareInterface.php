<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface ModelAwareInterface
{
    public static function setModel(ModelInterface $model);

    /**
     * @return ModelInterface
     */
    public static function getModel();
}