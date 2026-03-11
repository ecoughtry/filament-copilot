<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Tools;

use Filament\Facades\Filament;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetCurrentPageTool extends BaseTool
{
    public function description(): Stringable|string
    {
        return 'Get information about the current panel, available resources, and pages.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Stringable|string
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel) {
            return 'No active panel.';
        }

        $lines = [
            "Current Panel: {$panel->getId()}",
            '',
            'Available Resources:',
        ];

        foreach ($panel->getResources() as $resourceClass) {
            $lines[] = "  - {$resourceClass::getPluralModelLabel()} (/{$resourceClass::getSlug()})";
        }

        $lines[] = '';
        $lines[] = 'Available Pages:';

        foreach ($panel->getPages() as $pageClass) {
            $lines[] = "  - {$pageClass::getNavigationLabel()} (/{$pageClass::getSlug()})";
        }

        return implode("\n", $lines);
    }
}
