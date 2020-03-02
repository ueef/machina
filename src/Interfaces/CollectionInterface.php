<?php

declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

use Iterator;

interface CollectionInterface extends Iterator
{
    public function current();
    public function next(): void;
    public function key(): int;
    public function valid(): bool;
    public function rewind(): void;
    public function index(): array;
}