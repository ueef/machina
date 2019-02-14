<?php
declare(strict_types=1);

namespace Ueef\Machina\Properties;

use Ueef\Machina\Interfaces\PropertyInterface;
use Ueef\Machina\Exceptions\PropertyValidationException;

abstract class AbstractProperty implements PropertyInterface
{
    /** @var bool */
    protected $generated;

    /** @var bool */
    protected $identified;


    public function __construct(bool $identified = false, bool $generated = false)
    {
        $this->generated = $generated;
        $this->identified = $identified;
    }

    public function isGenerated(): bool
    {
        return $this->generated;
    }

    public function isIdentified(): bool
    {
        return $this->identified;
    }
}