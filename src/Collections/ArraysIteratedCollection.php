<?php

declare(strict_types=1);

namespace Ueef\Machina\Collections;

use Ueef\Machina\Interfaces\ArraysCollectionInterface;

class ArraysIteratedCollection extends AbstractIteratedCollection implements ArraysCollectionInterface
{
    /**
     * @return array[]
     */
    public function index(): array
    {
        return parent::index();
    }

    public function current(): array
    {
        return parent::current();
    }
}