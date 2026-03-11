<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Tools\Concerns;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

trait ValidatesAuthorization
{
    protected function authorizeView(string $resourceClass, Model $record): bool
    {
        if (! config('filament-copilot.respect_authorization', true)) {
            return true;
        }

        return $resourceClass::canView($record);
    }

    protected function authorizeCreate(string $resourceClass): bool
    {
        if (! config('filament-copilot.respect_authorization', true)) {
            return true;
        }

        return $resourceClass::canCreate();
    }

    protected function authorizeEdit(string $resourceClass, Model $record): bool
    {
        if (! config('filament-copilot.respect_authorization', true)) {
            return true;
        }

        return $resourceClass::canEdit($record);
    }

    protected function authorizeDelete(string $resourceClass, Model $record): bool
    {
        if (! config('filament-copilot.respect_authorization', true)) {
            return true;
        }

        return $resourceClass::canDelete($record);
    }

    protected function resolveResource(string $slug): ?string
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel) {
            return null;
        }

        foreach ($panel->getResources() as $resourceClass) {
            if ($resourceClass::getSlug() === $slug) {
                return $resourceClass;
            }
        }

        return null;
    }
}
