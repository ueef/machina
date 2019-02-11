<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface TransactionalDriverInterface
{
    public function begin(): void;
    public function commit(): void;
    public function rollback(): void;
}