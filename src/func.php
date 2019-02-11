<?php
declare(strict_types=1);

namespace Ueef\Machina;

function array_separate(array $array)
{
    return [array_keys($array), array_values($array)];
}

