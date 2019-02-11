<?php
declare(strict_types=1);

namespace Ueef\Machina\Sequences;

use Ueef\Machina\Interfaces\SequenceInterface;

class RandomSequence implements SequenceInterface
{
    public function next(): int
    {
        return $this->nextFew()[0];
    }

    public function nextFew(int $length = 1): array
    {
        $keys = [];
        for ($i=0; $i<$length; $i++) {
            $keys = random_int(0, PHP_INT_MAX);
        }

        return $keys;
    }
}