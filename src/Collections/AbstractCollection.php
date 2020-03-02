<?php

declare(strict_types=1);

namespace Ueef\Machina\Collections;

use Ueef\Machina\Interfaces\CollectionInterface;

abstract class AbstractCollection implements CollectionInterface
{
    private array $items;
    private int   $cursor;
    private int   $length;

    public function __construct(array $items)
    {
        $this->items = $items;
        $this->length = count($items);
        $this->rewind();
    }

    public function current()
    {
        return $this->items[$this->cursor];
    }

    public function next(): void
    {
        $this->cursor++;
    }

    public function key(): int
    {
        return $this->cursor;
    }

    public function valid(): bool
    {
        return $this->cursor < $this->length;
    }

    public function rewind(): void
    {
        $this->cursor = 0;
    }

    public function index(): array
    {
        return $this->items;
    }

}