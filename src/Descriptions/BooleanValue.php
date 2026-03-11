<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Descriptions;

class BooleanValue extends DescriptionValue
{
    protected ?string $trueLabel = null;

    protected ?string $falseLabel = null;

    public function trueLabel(string $label): static
    {
        $this->trueLabel = $label;

        return $this;
    }

    public function falseLabel(string $label): static
    {
        $this->falseLabel = $label;

        return $this;
    }

    public function getType(): string
    {
        return 'boolean';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), array_filter([
            'true_label' => $this->trueLabel,
            'false_label' => $this->falseLabel,
        ]));
    }
}
