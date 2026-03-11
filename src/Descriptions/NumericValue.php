<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Descriptions;

class NumericValue extends DescriptionValue
{
    protected ?float $min = null;

    protected ?float $max = null;

    protected ?string $unit = null;

    public function min(float $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function max(float $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function unit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getType(): string
    {
        return 'numeric';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), array_filter([
            'min' => $this->min,
            'max' => $this->max,
            'unit' => $this->unit,
        ]));
    }
}
