<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Agent;

use EslamRedaDiv\FilamentCopilot\Enums\PlanStatus;
use EslamRedaDiv\FilamentCopilot\Events\CopilotPlanApproved;
use EslamRedaDiv\FilamentCopilot\Events\CopilotPlanProposed;
use EslamRedaDiv\FilamentCopilot\Events\CopilotPlanRejected;
use EslamRedaDiv\FilamentCopilot\Models\CopilotConversation;
use EslamRedaDiv\FilamentCopilot\Models\CopilotPlan;

class PlanningEngine
{
    /**
     * Create a new plan from the agent's proposed steps.
     */
    public function propose(
        CopilotConversation $conversation,
        string $content,
        array $steps,
        ?string $messageId = null,
    ): CopilotPlan {
        $plan = $conversation->plans()->create([
            'message_id' => $messageId,
            'plan_content' => $content,
            'steps' => $steps,
            'status' => PlanStatus::Proposed,
            'current_step' => 0,
        ]);

        event(new CopilotPlanProposed($plan, $conversation));

        return $plan;
    }

    /**
     * Approve a proposed plan for execution.
     */
    public function approve(CopilotPlan $plan): void
    {
        $plan->approve();

        event(new CopilotPlanApproved($plan, $plan->conversation));
    }

    /**
     * Reject a proposed plan.
     */
    public function reject(CopilotPlan $plan, ?string $reason = null): void
    {
        $plan->reject();

        event(new CopilotPlanRejected($plan, $plan->conversation, $reason));
    }

    /**
     * Start executing an approved plan.
     */
    public function startExecution(CopilotPlan $plan): void
    {
        $plan->startExecution();
    }

    /**
     * Advance to the next step in a plan.
     */
    public function advanceStep(CopilotPlan $plan): bool
    {
        $plan->advanceStep();

        if ($plan->current_step >= $plan->total_steps) {
            $plan->complete();

            return false;
        }

        return true;
    }

    /**
     * Mark a plan as failed.
     */
    public function fail(CopilotPlan $plan): void
    {
        $plan->fail();
    }

    /**
     * Get the currently active plan for a conversation.
     */
    public function getActivePlan(CopilotConversation $conversation): ?CopilotPlan
    {
        return $conversation->plans()
            ->whereIn('status', [PlanStatus::Proposed, PlanStatus::Approved, PlanStatus::Executing])
            ->latest()
            ->first();
    }
}
