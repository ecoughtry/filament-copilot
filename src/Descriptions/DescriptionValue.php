<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Descriptions;

use Stringable;

abstract class DescriptionValue implements Stringable
{
    protected string $label;

    protected ?string $description = null;

    protected bool $required = false;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public static function make(string $label): static
    {
        return new static($label);
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    abstract public function getType(): string;

    public function toArray(): array
    {
        return array_filter([
            'label' => $this->label,
            'type' => $this->getType(),
            'description' => $this->description,
            'required' => $this->required,
        ]);
    }

    public function __toString(): string
    {
        $parts = ["{$this->label} ({$this->getType()})"];

        if ($this->description) {
            $parts[] = "- {$this->description}";
        }

        if ($this->required) {
            $parts[] = '[required]';
        }

        return implode(' ', $parts);
    }
}
