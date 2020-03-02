<?php

declare(strict_types=1);

namespace Ueef\Machina\Collections;

use Ueef\Machina\Interfaces\ObjectsCollectionInterface;

class ObjectsCollection extends AbstractCollection implements ObjectsCollectionInterface
{
    /**
     * @return object[]
     */
    public function index(): array
    {
        return parent::index();
    }

    public function current(): object
    {
        return parent::current();
    }
}