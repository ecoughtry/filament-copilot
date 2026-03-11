<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Resources\CopilotRateLimits\Pages;

use EslamRedaDiv\FilamentCopilot\Resources\CopilotRateLimits\CopilotRateLimitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCopilotRateLimits extends ListRecords
{
    protected static string $resource = CopilotRateLimitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
