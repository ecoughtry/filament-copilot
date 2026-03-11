<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Resources\CopilotConversations\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CopilotConversationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament-copilot::filament-copilot.conversation_details'))
                    ->schema([
                        TextEntry::make('title')
                            ->label(__('filament-copilot::filament-copilot.title')),
                        TextEntry::make('panel_id')
                            ->label(__('filament-copilot::filament-copilot.panel'))
                            ->badge(),
                        TextEntry::make('participant_type')
                            ->label(__('filament-copilot::filament-copilot.participant_type')),
                        TextEntry::make('participant_id')
                            ->label(__('filament-copilot::filament-copilot.participant_id')),
                        TextEntry::make('created_at')
                            ->label(__('filament-copilot::filament-copilot.created_at'))
                            ->dateTime(),
                    ])->columns(3),
                Section::make(__('filament-copilot::filament-copilot.messages'))
                    ->schema([
                        RepeatableEntry::make('messages')
                            ->schema([
                                TextEntry::make('role')
                                    ->label(__('filament-copilot::filament-copilot.role'))
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'user' => 'info',
                                        'assistant' => 'success',
                                        'system' => 'warning',
                                        'tool' => 'gray',
                                        default => 'gray',
                                    }),
                                TextEntry::make('content')
                                    ->label(__('filament-copilot::filament-copilot.content'))
                                    ->markdown()
                                    ->columnSpanFull(),
                                TextEntry::make('input_tokens')
                                    ->label(__('filament-copilot::filament-copilot.input_tokens')),
                                TextEntry::make('output_tokens')
                                    ->label(__('filament-copilot::filament-copilot.output_tokens')),
                                TextEntry::make('created_at')
                                    ->label(__('filament-copilot::filament-copilot.time'))
                                    ->dateTime(),
                            ])->columns(4),
                    ]),
            ]);
    }
}
