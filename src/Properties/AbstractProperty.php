<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

use Ueef\Machina\Interfaces\PropertyInterface;
use Ueef\Machina\Exceptions\PropertyValidationException;

abstract class AbstractProperty implements PropertyInterface
{
    /** @var bool */
    protected $generated;


    public function __construct(bool $generated = false)
    {
        $this->generated = $generated;
    }

    public function isGenerated(): bool
    {
        return $this->generated;
    }
}