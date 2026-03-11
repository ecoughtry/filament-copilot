<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Resources\CopilotRateLimits\Pages;

use EslamRedaDiv\FilamentCopilot\Resources\CopilotRateLimits\CopilotRateLimitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCopilotRateLimit extends CreateRecord
{
    protected static string $resource = CopilotRateLimitResource::class;
}
