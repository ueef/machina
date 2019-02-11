<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface ModelAwareInterface
{
    public static function injectModel(ModelInterface $model);
}