<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Descriptions;

class RelationValue extends DescriptionValue
{
    protected ?string $relatedModel = null;

    protected ?string $displayField = null;

    public function relatedModel(string $model): static
    {
        $this->relatedModel = $model;

        return $this;
    }

    public function displayField(string $field): static
    {
        $this->displayField = $field;

        return $this;
    }

    public function getType(): string
    {
        return 'relation';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), array_filter([
            'related_model' => $this->relatedModel,
            'display_field' => $this->displayField,
        ]));
    }
}
