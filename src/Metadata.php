<?php
declare(strict_types=1);

namespace Ueef\Machina;

use Ueef\Machina\Interfaces\GeneratorInterface;
use Ueef\Machina\Interfaces\MetadataInterface;
use Ueef\Machina\Interfaces\PropertyInterface;

class Metadata implements MetadataInterface
{
    /** @var string */
    private $source;

    /** @var PropertyInterface[] */
    private $properties;

    /** @var GeneratorInterface */
    private $generator;

    /** @var string */
    private $generation_strategy;

    /** @var PropertyInterface[] */
    private $generated_properties;

    /** @var PropertyInterface[] */
    private $identified_properties;


    public function __construct(string $source, array $properties, string $generationStrategy = self::GENERATION_STRATEGY_AUTO, ?GeneratorInterface $generator = null)
    {
        $this->source = $source;
        $this->generator = $generator;
        $this->generation_strategy = $generationStrategy;

        foreach ($properties as $key => $property) {
            $this->properties[$key] = $property;
            if ($property->isGenerated()) {
                $this->generated_properties[$key] = $property;
            }
            if ($property->isIdentified()) {
                $this->identified_properties[$key] = $property;
            }
        }
    }

    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return PropertyInterface[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return PropertyInterface[]
     */
    public function getGeneratedProperties(): array
    {
        return $this->generated_properties;
    }

    /**
     * @return PropertyInterface[]
     */
    public function getIdentifiedProperties(): array
    {
        return $this->identified_properties;
    }

    public function getGenerator(): ?GeneratorInterface
    {
        return $this->generator;
    }

    public function getGenerationStrategy(): string
    {
        return $this->generation_strategy;
    }
}