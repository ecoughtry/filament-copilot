<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Concerns;

use Laravel\Ai\Contracts\Tool;

/**
 * Add to Filament Widget classes to expose them to the Copilot agent.
 */
trait HasCopilotWidgetContext
{
    public function copilotWidgetDescription(): ?string
    {
        return 'Widget: '.class_basename(static::class);
    }

    public function copilotWidgetData(): array
    {
        return [];
    }

    /**
     * Return custom tools for this widget.
     *
     * @return array<Tool>
     */
    public function copilotTools(): array
    {
        return [];
    }
}
