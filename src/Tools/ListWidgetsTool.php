<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Tools;

use EslamRedaDiv\FilamentCopilot\Discovery\WidgetInspector;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListWidgetsTool extends BaseTool
{
    public function __construct(
        protected WidgetInspector $widgetInspector,
    ) {}

    public function description(): Stringable|string
    {
        return 'List all available dashboard widgets in the current panel with their descriptions and data availability.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Stringable|string
    {
        $widgets = $this->widgetInspector->discoverWidgets($this->panelId ?? null);

        if (empty($widgets)) {
            return 'No widgets available in this panel.';
        }

        $lines = ['Available Widgets:', ''];

        foreach ($widgets as $widget) {
            $line = "- {$widget['name']}";

            if (! empty($widget['description'])) {
                $line .= ": {$widget['description']}";
            }

            if ($widget['exposes_data'] ?? false) {
                $line .= ' [data available]';
            }

            if ($widget['has_copilot_trait'] ?? false) {
                $line .= ' [copilot-enabled]';
            }

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }
}
