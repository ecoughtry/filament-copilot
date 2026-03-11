<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CopilotAgentMemory extends Model
{
    use HasUlids;

    protected $fillable = [
        'participant_type',
        'participant_id',
        'panel_id',
        'tenant_type',
        'tenant_id',
        'key',
        'value',
    ];

    public function participant(): MorphTo
    {
        return $this->morphTo();
    }

    public function tenant(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForPanel($query, string $panelId)
    {
        return $query->where('panel_id', $panelId);
    }

    public function scopeForParticipant($query, Model $participant)
    {
        return $query
            ->where('participant_type', $participant->getMorphClass())
            ->where('participant_id', $participant->getKey());
    }

    public function scopeForTenant($query, ?Model $tenant)
    {
        if ($tenant === null) {
            return $query->whereNull('tenant_type')->whereNull('tenant_id');
        }

        return $query
            ->where('tenant_type', $tenant->getMorphClass())
            ->where('tenant_id', $tenant->getKey());
    }

    public static function remember(
        Model $participant,
        string $panelId,
        string $key,
        string $value,
        ?Model $tenant = null,
    ): static {
        return static::updateOrCreate(
            [
                'participant_type' => $participant->getMorphClass(),
                'participant_id' => $participant->getKey(),
                'panel_id' => $panelId,
                'tenant_type' => $tenant?->getMorphClass(),
                'tenant_id' => $tenant?->getKey(),
                'key' => $key,
            ],
            ['value' => $value],
        );
    }

    public static function recall(
        Model $participant,
        string $panelId,
        string $key,
        ?Model $tenant = null,
    ): ?string {
        return static::query()
            ->forParticipant($participant)
            ->forPanel($panelId)
            ->forTenant($tenant)
            ->where('key', $key)
            ->value('value');
    }

    public static function recallAll(
        Model $participant,
        string $panelId,
        ?Model $tenant = null,
    ): array {
        return static::query()
            ->forParticipant($participant)
            ->forPanel($panelId)
            ->forTenant($tenant)
            ->pluck('value', 'key')
            ->toArray();
    }

    public static function forget(
        Model $participant,
        string $panelId,
        string $key,
        ?Model $tenant = null,
    ): void {
        static::query()
            ->forParticipant($participant)
            ->forPanel($panelId)
            ->forTenant($tenant)
            ->where('key', $key)
            ->delete();
    }
}
