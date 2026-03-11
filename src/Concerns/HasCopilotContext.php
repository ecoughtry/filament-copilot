<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Concerns;

use Laravel\Ai\Contracts\Tool;

/**
 * Add to Filament Resource classes to expose them to the Copilot agent.
 */
trait HasCopilotContext
{
    public function copilotResourceDescription(): string
    {
        $model = static::getModel();
        $label = static::getModelLabel();
        $pluralLabel = static::getPluralModelLabel();

        return "Manages {$pluralLabel} ({$model}). Label: {$label}.";
    }

    public function copilotRecordDescription($record): array
    {
        $label = static::getModelLabel();
        $key = $record->getKey();

        return ["{$label} #{$key}"];
    }

    /**
     * Return custom tools for this resource.
     *
     * @return array<Tool>
     */
    public function copilotTools(): array
    {
        return [];
    }

    /**
     * Fields the AI is allowed to read.
     *
     * @return array<string>|null null means all visible fields
     */
    public static function copilotReadableFields(): ?array
    {
        return null;
    }

    /**
     * Fields the AI is allowed to fill/write.
     *
     * @return array<string>|null null means all fillable fields
     */
    public static function copilotWritableFields(): ?array
    {
        return null;
    }

    /**
     * Whether the AI can create records for this resource.
     */
    public static function copilotCanCreate(): bool
    {
        return true;
    }

    /**
     * Whether the AI can delete records for this resource.
     */
    public static function copilotCanDelete(): bool
    {
        return false;
    }
}
