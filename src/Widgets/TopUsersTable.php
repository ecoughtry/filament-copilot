<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Widgets;

use EslamRedaDiv\FilamentCopilot\Models\CopilotTokenUsage;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopUsersTable extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('filament-copilot::filament-copilot.top_users_this_month'))
            ->query(
                CopilotTokenUsage::query()
                    ->where('usage_date', '>=', now()->startOfMonth()->toDateString())
                    ->selectRaw('participant_type, participant_id, SUM(total_tokens) as total, SUM(input_tokens) as input_sum, SUM(output_tokens) as output_sum, COUNT(*) as request_count')
                    ->groupBy('participant_type', 'participant_id')
                    ->orderByDesc('total')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('participant_id')
                    ->label(__('filament-copilot::filament-copilot.user')),
                Tables\Columns\TextColumn::make('request_count')
                    ->label(__('filament-copilot::filament-copilot.requests'))
                    ->numeric(),
                Tables\Columns\TextColumn::make('input_sum')
                    ->label(__('filament-copilot::filament-copilot.input_tokens'))
                    ->numeric(),
                Tables\Columns\TextColumn::make('output_sum')
                    ->label(__('filament-copilot::filament-copilot.output_tokens'))
                    ->numeric(),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('filament-copilot::filament-copilot.total_tokens'))
                    ->numeric()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
