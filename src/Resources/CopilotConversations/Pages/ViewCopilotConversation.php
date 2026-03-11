<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Resources\CopilotConversations\Pages;

use EslamRedaDiv\FilamentCopilot\Resources\CopilotConversations\CopilotConversationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCopilotConversation extends ViewRecord
{
    protected static string $resource = CopilotConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
