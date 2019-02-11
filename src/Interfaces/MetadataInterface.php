<?php
declare(strict_types=1);

namespace Ueef\Machina\Interfaces;

interface MetadataInterface
{
    const GENERATION_STRATEGY_NONE = 'none';
    const GENERATION_STRATEGY_AUTO = 'auto';
    const GENERATION_STRATEGY_CUSTOM = 'custom';

    /**
     * @return string
     */
    public function getSource(): string;

    /**
     * @return PropertyInterface[]
     */
    public function getProperties(): array;

    /**
     * @return GeneratorInterface|null
     */
    public function getGenerator(): ?GeneratorInterface;

    /**
     * @return string
     */
    public function getGenerationStrategy(): string;

    /**
     * @return PropertyInterface[]
     */
    public function getGeneratedProperties(): array;
}