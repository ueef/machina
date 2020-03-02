<?php
declare(strict_types=1);

namespace Ueef\Machina\Drivers;

use Ueef\Machina\Collections\ArraysCollection;
use Ueef\Machina\Exceptions\DriverException;
use Ueef\Machina\Interfaces\ArraysCollectionInterface;
use Ueef\Machina\Interfaces\DriverInterface;
use Ueef\Machina\Interfaces\FilterInterface;
use Ueef\Machina\Interfaces\MetadataInterface;
use Ueef\Machina\Interfaces\PropertyInterface;

class StubDriver implements DriverInterface
{
    /** @var array[] */
    private $data;

    /** @var array */
    private $locks = [];

    /** @var array[] */
    private $transactions = [];


    public function __construct(array $data = [])
    {
        foreach ($data as $source => $rows) {
            $this->setRows($source, $rows);
        }
    }

    public function find(MetadataInterface $metadata, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): ArraysCollectionInterface
    {
        $rows = $this->getRows($metadata->getSource());
        $rows = $this->filterRows($rows, $filters);
        $rows = $this->orderRows($rows, $orders);

        if ($limit) {
            $rows = array_slice($rows, $offset, $limit);
        } else {
            $rows = array_slice($rows, $offset, null);
        }

        return new ArraysCollection($rows);
    }

    public function count(MetadataInterface $metadata, array $filters = []): int
    {
        $rows = $this->getRows($metadata->getSource());
        $rows = $this->filterRows($rows, $filters);

        return count($rows);
    }

    public function insert(MetadataInterface $metadata, array &$rows): void
    {
        $_rows = $this->getRows($metadata->getSource());
        if (MetadataInterface::GENERATION_STRATEGY_AUTO == $metadata->getGenerationStrategy()) {
            foreach ($metadata->getGeneratedProperties() as $key => $property) {
                $value = 0;
                foreach ($_rows as $row) {
                    if (isset($row[$key])) {
                        $value = max($value, $row[$key]);
                    }
                }

                foreach ($rows as &$row) {
                    if (isset($row[$key])) {
                        break;
                    }

                    switch ($property->getType()) {
                        case PropertyInterface::TYPE_INT:
                            $value++;
                            $row[$key] = (int) $value;
                            break;
                        default:
                            throw new DriverException(["cannot generate the value of type \"%s\" automatically", $property->getType()]);
                    }
                }
            }
        }

        $this->setRows($metadata->getSource(), array_merge($_rows, $rows));
    }

    public function update(MetadataInterface $metadata, array $values, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
    {
        $rows = $this->getRows($metadata->getSource());

        $_rows = $this->filterRows($rows, $filters);
        $_rows = $this->orderRows($_rows, $orders);
        if ($limit) {
            $_rows = array_slice($_rows, $offset, $limit, true);
        } else {
            $_rows = array_slice($_rows, $offset, null, true);
        }

        foreach ($_rows as  $i => $row) {
            foreach ($values as $k => $v) {
                $row[$k] = $v;
            }
            $rows[$i] = $row;
        }

        $this->setRows($metadata->getSource(), $rows);
    }

    public function delete(MetadataInterface $metadata, array $filters = [], array $orders = [], int $limit = 0, int $offset = 0): void
    {
        $rows = $this->getRows($metadata->getSource());

        $_rows = $this->filterRows($rows, $filters);
        $_rows = $this->orderRows($_rows, $orders);
        if ($limit) {
            $_rows = array_slice($_rows, $offset, $limit, true);
        } else {
            $_rows = array_slice($_rows, $offset, null, true);
        }

        foreach ($_rows as  $i => $row) {
            unset($rows[$i]);
        }

        $this->setRows($metadata->getSource(), $rows);
    }

    public function lock(MetadataInterface $metadata, string $resource, bool $wait = true): bool
    {
        if (in_array($resource, $this->locks)) {
            return false;
        }

        $this->locks[] = $resource;

        return true;
    }

    public function unlock(MetadataInterface $metadata, string $resource): bool
    {
        if (!in_array($resource, $this->locks)) {
            return false;
        }

        $this->locks = array_diff($this->locks, [$resource]);

        return true;
    }

    public function begin(): void
    {
        $this->transactions[] = $this->data;
    }

    public function commit(): void
    {
        if ($this->transactions) {
            array_pop($this->transactions);
        }
    }

    public function rollback(): void
    {
        if ($this->transactions) {
            $this->data = array_pop($this->transactions);
        }
    }

    private function getRows(string $source): array
    {
        if (!isset($this->data[$source])) {
            throw new DriverException(["source \"%s\" doesn't exist", $source]);
        }

        return $this->data[$source];
    }

    private function setRows(string $source, array $rows): void
    {
        $this->data[$source] = array_values($rows);
    }

    private function orderRows(array $rows, array $orders): array
    {
        $_rows = [];
        foreach ($rows as $i => $row) {
            $_rows[$i] = $row;
        }

        return $_rows;
    }

    private function filterRows(array $rows, array $filters): array
    {
        $_rows = [];
        foreach ($rows as $i => $row) {
            if ($this->compareFilters($row, $filters)) {
                $_rows[$i] = $row;
            }
        }

        return $_rows;
    }

    private function compareFilters(array $row, array $filters): bool
    {
        if (!$filters) {
            return true;
        }

        foreach ($filters as $filter) {
            if (!$this->compareFilter($row, ...$filter)) {
                return false;
            }
        }

        return true;
    }

    private function compareFilter(array $row, string $operator, ...$operands): bool
    {
        switch (count($operands)) {
            case 1:
                return $this->compareFilterConjunction($row, $operator, ...$operands);
            case 2:
                return $this->compareFilterCondition($row, $operator, ...$operands);
        }

        throw new DriverException("wrong number of operands in %s", array_slice(func_get_args(), 1));
    }

    private function compareFilterCondition(array $row, string $operator, string $key, $operand): bool
    {
        if (!isset($row[$key])) {
            throw new DriverException(["undefined property \"%s\"", $key]);
        }

        switch ($operator) {
            case FilterInterface::EQ:
                if (is_array($operand)) {
                    return in_array($row[$key], $operand);
                } else {
                    return $row[$key] == $operand;
                }

            case FilterInterface::NE:
                if (is_array($operand)) {
                    return !in_array($row[$key], $operand);
                } else {
                    return $row[$key] != $operand;
                }

            case FilterInterface::GT:
                if (is_array($operand)) {
                    return $row[$key] > max($operand);
                } else {
                    return $row[$key] > $operand;
                }

            case FilterInterface::LT:
                if (is_array($operand)) {
                    return $row[$key] < min($operand);
                } else {
                    return $row[$key] < $operand;
                }

            case FilterInterface::GE:
                if (is_array($operand)) {
                    return $row[$key] >= max($operand);
                } else {
                    return $row[$key] >= $operand;
                }

            case FilterInterface::LE:
                if (is_array($operand)) {
                    return $row[$key] <= min($operand);
                } else {
                    return $row[$key] <= $operand;
                }
        }

        throw new DriverException(["unsupported operator \"%s\" in %s", $operator, array_slice(func_get_args(), 1)]);
    }

    private function compareFilterConjunction(array $row, string $operator, array $filters): bool
    {
        $result = [];
        foreach ($filters as $filter) {
            $result[] = $this->compareFilter($row, ...$filter);
        }

        switch ($operator) {
            case FilterInterface::OR:
                return in_array(true, $filters, true);
                break;

            case FilterInterface::AND:
                return in_array(false, $filters, true);
                break;
        }

        throw new DriverException(["unsupported operator \"%s\" in %s", $operator, array_slice(func_get_args(), 1)]);
    }
}