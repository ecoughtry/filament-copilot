<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Descriptions;

class ListValue extends DescriptionValue
{
    protected array $options = [];

    /**
     * @param  array<string, string>  $options  key => label pairs
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getType(): string
    {
        return 'list';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
        ]);
    }
}
