<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface FilterInterface
{
    const EQ = '=';
    const NE = '!=';
    const GT = '>';
    const LT = '<';
    const GE = '>=';
    const LE = '<=';
    const OR = 'or';
    const AND = 'and';
    const XOR = 'xor';
}