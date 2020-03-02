<?php

declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

use Iterator;

interface ObjectsCollectionInterface extends CollectionInterface
{
    /** @return object[] */
    public function index(): array;
    public function current(): object;
}