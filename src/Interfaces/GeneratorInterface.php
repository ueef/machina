<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface GeneratorInterface
{
    public function generate(MetadataInterface $metadata, array &$items): void;
}