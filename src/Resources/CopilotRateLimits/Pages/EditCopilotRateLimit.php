<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Resources\CopilotRateLimits\Pages;

use EslamRedaDiv\FilamentCopilot\Resources\CopilotRateLimits\CopilotRateLimitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCopilotRateLimit extends EditRecord
{
    protected static string $resource = CopilotRateLimitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
