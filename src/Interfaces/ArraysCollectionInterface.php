<?php

declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface ArraysCollectionInterface extends CollectionInterface
{
    /** @return array[] */
    public function index(): array;
    public function current(): array;
}