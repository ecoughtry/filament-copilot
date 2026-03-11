<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Resources\CopilotConversations\Pages;

use EslamRedaDiv\FilamentCopilot\Resources\CopilotConversations\CopilotConversationResource;
use Filament\Resources\Pages\ListRecords;

class ListCopilotConversations extends ListRecords
{
    protected static string $resource = CopilotConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
