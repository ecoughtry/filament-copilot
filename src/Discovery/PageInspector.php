<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Discovery;

use EslamRedaDiv\FilamentCopilot\Concerns\HasCopilotPageContext;
use Filament\Facades\Filament;

class PageInspector
{
    /**
     * Discover all pages in the current panel.
     */
    public function discoverPages(?string $panelId = null): array
    {
        $panel = $panelId
            ? Filament::getPanel($panelId)
            : Filament::getCurrentPanel();

        if (! $panel) {
            return [];
        }

        $pages = [];

        foreach ($panel->getPages() as $pageClass) {
            $pageData = [
                'page' => $pageClass,
                'label' => $pageClass::getNavigationLabel(),
                'slug' => $pageClass::getSlug(),
                'url' => $pageClass::getUrl(),
            ];

            if (in_array(HasCopilotPageContext::class, class_uses_recursive($pageClass))) {
                $instance = app($pageClass);
                $pageData['copilot_description'] = $instance->copilotPageDescription();
                $pageData['copilot_tools'] = $instance->copilotTools();
            }

            $pages[] = $pageData;
        }

        return $pages;
    }

    /**
     * Build AI-friendly page descriptions.
     */
    public function buildPageContext(?string $panelId = null): string
    {
        $pages = $this->discoverPages($panelId);

        if (empty($pages)) {
            return 'No pages available.';
        }

        $lines = ['Available Pages:'];

        foreach ($pages as $page) {
            $line = "- {$page['label']} (/{$page['slug']})";

            if (! empty($page['copilot_description'])) {
                $line .= " — {$page['copilot_description']}";
            }

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }
}
