<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Resources\CopilotAuditLogs\Pages;

use EslamRedaDiv\FilamentCopilot\Resources\CopilotAuditLogs\CopilotAuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListCopilotAuditLogs extends ListRecords
{
    protected static string $resource = CopilotAuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
