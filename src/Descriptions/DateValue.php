<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Descriptions;

class DateValue extends DescriptionValue
{
    protected ?string $format = null;

    protected bool $includesTime = false;

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function withTime(bool $includesTime = true): static
    {
        $this->includesTime = $includesTime;

        return $this;
    }

    public function getType(): string
    {
        return $this->includesTime ? 'datetime' : 'date';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), array_filter([
            'format' => $this->format,
        ]));
    }
}
