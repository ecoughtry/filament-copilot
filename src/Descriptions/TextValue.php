<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Descriptions;

class TextValue extends DescriptionValue
{
    protected ?int $maxLength = null;

    protected ?string $format = null;

    public function maxLength(int $maxLength): static
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getType(): string
    {
        return 'text';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), array_filter([
            'max_length' => $this->maxLength,
            'format' => $this->format,
        ]));
    }
}
