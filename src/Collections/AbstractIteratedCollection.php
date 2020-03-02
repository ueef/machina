<?php

declare(strict_types=1);

namespace Ueef\Machina\Collections;

use Iterator;
use Ueef\Machina\Interfaces\CollectionInterface;

abstract class AbstractIteratedCollection implements CollectionInterface
{
    private Iterator $iterator;


    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    public function index(): array
    {
        $items = [];
        foreach ($this as $item) {
            $items[] = $item;
        }

        return $items;
    }

    public function current()
    {
        return $this->iterator->current();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key(): int
    {
        return $this->iterator->key();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->iterator->valid();
    }
}