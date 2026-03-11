<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Discovery;

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
            $pages[] = [
                'page' => $pageClass,
                'label' => $pageClass::getNavigationLabel(),
                'slug' => $pageClass::getSlug(),
                'url' => $pageClass::getUrl(),
            ];
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
            $lines[] = "- {$page['label']} (/{$page['slug']})";
        }

        return implode("\n", $lines);
    }
}
