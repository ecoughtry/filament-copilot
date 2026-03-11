<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Models;

use EslamRedaDiv\FilamentCopilot\Enums\PlanStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CopilotPlan extends Model
{
    use HasUlids;

    protected $fillable = [
        'conversation_id',
        'message_id',
        'plan_content',
        'steps',
        'status',
        'current_step',
    ];

    protected function casts(): array
    {
        return [
            'steps' => 'array',
            'status' => PlanStatus::class,
            'current_step' => 'integer',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(CopilotConversation::class, 'conversation_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(CopilotMessage::class, 'message_id');
    }

    public function isProposed(): bool
    {
        return $this->status === PlanStatus::Proposed;
    }

    public function isApproved(): bool
    {
        return $this->status === PlanStatus::Approved;
    }

    public function isExecuting(): bool
    {
        return $this->status === PlanStatus::Executing;
    }

    public function isCompleted(): bool
    {
        return $this->status === PlanStatus::Completed;
    }

    public function approve(): void
    {
        $this->update(['status' => PlanStatus::Approved]);
    }

    public function reject(): void
    {
        $this->update(['status' => PlanStatus::Rejected]);
    }

    public function startExecution(): void
    {
        $this->update(['status' => PlanStatus::Executing]);
    }

    public function advanceStep(): void
    {
        $this->increment('current_step');
    }

    public function complete(): void
    {
        $this->update(['status' => PlanStatus::Completed]);
    }

    public function fail(): void
    {
        $this->update(['status' => PlanStatus::Failed]);
    }

    public function getTotalStepsAttribute(): int
    {
        return is_array($this->steps) ? count($this->steps) : 0;
    }

    public function getCurrentStepDescriptionAttribute(): ?string
    {
        if (! is_array($this->steps) || ! isset($this->steps[$this->current_step])) {
            return null;
        }

        return $this->steps[$this->current_step]['description'] ?? null;
    }
}
