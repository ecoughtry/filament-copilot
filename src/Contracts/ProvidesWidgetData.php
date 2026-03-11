<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Contracts;

interface ProvidesWidgetData
{
    /**
     * Provide the widget's current data for the AI agent to read.
     */
    public function copilotWidgetData(): array;

    /**
     * Describe what this widget shows, for the AI agent's context.
     */
    public function copilotWidgetDescription(): ?string;
}
