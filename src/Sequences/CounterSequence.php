<?php
declare(strict_types=1);

namespace Ueef\Machina\Sequences;

use Ueef\Machina\Exceptions\SequenceException;
use Ueef\Machina\Interfaces\DriverInterface;
use Ueef\Machina\Interfaces\FilterInterface;
use Ueef\Machina\Interfaces\SequenceInterface;

class CounterSequence implements SequenceInterface
{
    /** @var string */
    private $table = 'sequence';

    /** @var string */
    private $scope;

    /** @var DriverInterface */
    private $driver;


    public function __construct(string $scope, DriverInterface $driver, string $table = '')
    {
        $this->scope = $scope;
        $this->driver = $driver;
        if ($table) {
            $this->table = $table;
        }
    }

    public function next(): int
    {
        return $this->nextFew()[0];
    }

    public function nextFew(int $length = 1): array
    {
        $start = 0;
        $end = 0;
        $this->driver->transact(function () use ($length, &$start, &$end) {
            $start = $this->getOrCreate();
            $end = $start + $length;
            $this->set($end);
        }, true);

        return range($start, $end-1);
    }

    private function get(string $scope): int
    {
        $counters = $this->driver->find($this->table, [[FilterInterface::EQ, 'scope', $scope]], [], 1, 0, true);
        if ($counters) {
            return (int) $counters[0]['value'];
        }

        return 0;
    }

    private function getOrCreate(): int
    {
        $value = $this->get($this->scope);
        if ($value) {
            return $value;
        }

        $this->driver->create($this->table, ['scope', 'value'], [$this->scope, 1]);

        $value = $this->get($this->scope);
        if (!$value) {
            throw new SequenceException(["cannot create counter"]);
        }

        return $value;
    }

    private function set(int $value)
    {
        $success = $this->driver->update($this->table, [
            'value' => $value,
        ], [
            [FilterInterface::EQ, 'scope', $this->scope],
        ]);

        if (!$success) {
            throw new SequenceException(["cannot update counter"]);
        }
    }
}