<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Tools;

use EslamRedaDiv\FilamentCopilot\Discovery\ResourceInspector;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListResourcesTool extends BaseTool
{
    public function __construct(
        protected ResourceInspector $resourceInspector,
    ) {}

    public function description(): Stringable|string
    {
        return 'List all available resources in the current panel with their descriptions, slugs, and available capabilities.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Stringable|string
    {
        $resources = $this->resourceInspector->discoverResources($this->panelId ?? null);

        if (empty($resources)) {
            return 'No resources available in this panel.';
        }

        $lines = ['Available Resources:', ''];

        foreach ($resources as $resource) {
            $line = "- {$resource['plural_label']} (slug: {$resource['slug']})";
            $line .= " | Model: {$resource['model']}";

            if ($resource['can_create'] ?? false) {
                $line .= ' | Can create';
            }

            if ($resource['has_copilot_trait'] ?? false) {
                $line .= ' | Copilot-enabled';

                if (! empty($resource['copilot_readable_fields'])) {
                    $line .= ' | Readable: '.implode(', ', $resource['copilot_readable_fields']);
                }

                if (! empty($resource['copilot_writable_fields'])) {
                    $line .= ' | Writable: '.implode(', ', $resource['copilot_writable_fields']);
                }
            }

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }
}
