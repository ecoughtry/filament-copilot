<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Concerns;

use Laravel\Ai\Contracts\Tool;

/**
 * Add to Filament Page classes to expose them to the Copilot agent.
 */
trait HasCopilotPageContext
{
    public function copilotPageDescription(): string
    {
        return 'Page: '.static::getNavigationLabel();
    }

    /**
     * Return custom tools for this page.
     *
     * @return array<Tool>
     */
    public function copilotTools(): array
    {
        return [];
    }
}
