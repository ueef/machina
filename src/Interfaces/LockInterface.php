<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface LockInterface
{
    public function getResource(): array;
}