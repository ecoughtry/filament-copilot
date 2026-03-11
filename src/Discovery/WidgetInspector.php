<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Discovery;

use EslamRedaDiv\FilamentCopilot\Concerns\HasCopilotWidgetContext;
use EslamRedaDiv\FilamentCopilot\Contracts\ProvidesWidgetData;
use Filament\Facades\Filament;

class WidgetInspector
{
    /**
     * Discover all widgets registered in the current panel.
     */
    public function discoverWidgets(?string $panelId = null): array
    {
        $panel = $panelId
            ? Filament::getPanel($panelId)
            : Filament::getCurrentPanel();

        if (! $panel) {
            return [];
        }

        $widgets = [];

        foreach ($panel->getWidgets() as $widgetClass) {
            $widgets[] = $this->inspectWidget($widgetClass);
        }

        return $widgets;
    }

    /**
     * Inspect a single widget class and return its metadata.
     */
    public function inspectWidget(string $widgetClass): array
    {
        $hasCopilotTrait = in_array(HasCopilotWidgetContext::class, class_uses_recursive($widgetClass));
        $providesData = is_subclass_of($widgetClass, ProvidesWidgetData::class)
            || in_array(ProvidesWidgetData::class, class_implements($widgetClass) ?: []);

        $data = [
            'widget' => $widgetClass,
            'name' => class_basename($widgetClass),
            'has_copilot_trait' => $hasCopilotTrait,
            'provides_data' => $providesData,
        ];

        if ($hasCopilotTrait || $providesData) {
            try {
                /** @var HasCopilotWidgetContext|ProvidesWidgetData $instance */
                $instance = app($widgetClass);

                if (method_exists($instance, 'copilotWidgetDescription')) {
                    $data['description'] = $instance->copilotWidgetDescription();
                }

                $data['exposes_data'] = true;
            } catch (\Throwable) {
                $data['description'] = 'Widget: '.class_basename($widgetClass);
                $data['exposes_data'] = false;
            }
        } else {
            $data['description'] = 'Widget: '.class_basename($widgetClass);
            $data['exposes_data'] = false;
        }

        return $data;
    }

    /**
     * Build AI-friendly widget descriptions for the system prompt.
     */
    public function buildWidgetContext(?string $panelId = null): string
    {
        $widgets = $this->discoverWidgets($panelId);

        if (empty($widgets)) {
            return '';
        }

        $lines = ['## Available Widgets'];

        foreach ($widgets as $widget) {
            $line = "- {$widget['name']}";

            if (! empty($widget['description'])) {
                $line .= ": {$widget['description']}";
            }

            if ($widget['exposes_data']) {
                $line .= ' [data available via GetWidgetDataTool]';
            }

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }
}
