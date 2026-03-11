<?php

use EslamRedaDiv\FilamentCopilot\Agent\PlanningEngine;
use EslamRedaDiv\FilamentCopilot\Enums\PlanStatus;
use EslamRedaDiv\FilamentCopilot\Models\CopilotConversation;
use EslamRedaDiv\FilamentCopilot\Models\CopilotPlan;

function createConversationForPlanning(): CopilotConversation
{
    $user = createTestUser();

    return CopilotConversation::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
    ]);
}

it('proposes a plan', function () {
    $conversation = createConversationForPlanning();
    $engine = app(PlanningEngine::class);

    $plan = $engine->propose(
        conversation: $conversation,
        content: 'Create a new user',
        steps: [
            ['description' => 'Fill the form', 'tool' => 'fill_form'],
            ['description' => 'Submit the form', 'tool' => 'create_record'],
        ],
    );

    expect($plan)->toBeInstanceOf(CopilotPlan::class)
        ->and($plan->status)->toBe(PlanStatus::Proposed)
        ->and($plan->plan_content)->toBe('Create a new user')
        ->and($plan->steps)->toHaveCount(2);
});

it('approves a plan', function () {
    $conversation = createConversationForPlanning();
    $engine = app(PlanningEngine::class);

    $plan = $engine->propose($conversation, 'Test plan', [
        ['description' => 'Step 1'],
    ]);

    $engine->approve($plan);

    expect($plan->fresh()->status)->toBe(PlanStatus::Approved);
});

it('rejects a plan', function () {
    $conversation = createConversationForPlanning();
    $engine = app(PlanningEngine::class);

    $plan = $engine->propose($conversation, 'Test plan', [
        ['description' => 'Step 1'],
    ]);

    $engine->reject($plan);

    expect($plan->fresh()->status)->toBe(PlanStatus::Rejected);
});

it('starts plan execution', function () {
    $conversation = createConversationForPlanning();
    $engine = app(PlanningEngine::class);

    $plan = $engine->propose($conversation, 'Test plan', [
        ['description' => 'Step 1'],
        ['description' => 'Step 2'],
    ]);

    $engine->approve($plan);
    $engine->startExecution($plan);

    expect($plan->fresh()->status)->toBe(PlanStatus::Executing)
        ->and($plan->fresh()->current_step)->toBe(0);
});

it('advances steps in execution', function () {
    $conversation = createConversationForPlanning();
    $engine = app(PlanningEngine::class);

    $plan = $engine->propose($conversation, 'Test', [
        ['description' => 'Step 1'],
        ['description' => 'Step 2'],
    ]);

    $engine->approve($plan);
    $engine->startExecution($plan);
    $engine->advanceStep($plan);

    expect($plan->fresh()->current_step)->toBe(1);
});

it('completes plan when all steps done', function () {
    $conversation = createConversationForPlanning();
    $engine = app(PlanningEngine::class);

    $plan = $engine->propose($conversation, 'Test', [
        ['description' => 'Only step'],
    ]);

    $engine->approve($plan);
    $engine->startExecution($plan);
    $engine->advanceStep($plan);

    expect($plan->fresh()->status)->toBe(PlanStatus::Completed);
});

it('can fail a plan', function () {
    $conversation = createConversationForPlanning();
    $engine = app(PlanningEngine::class);

    $plan = $engine->propose($conversation, 'Test', [
        ['description' => 'Step 1'],
    ]);

    $engine->approve($plan);
    $engine->startExecution($plan);
    $engine->fail($plan);

    expect($plan->fresh()->status)->toBe(PlanStatus::Failed);
});

it('gets active plan for conversation', function () {
    $conversation = createConversationForPlanning();
    $engine = app(PlanningEngine::class);

    $plan = $engine->propose($conversation, 'Active plan', [
        ['description' => 'Step 1'],
    ]);

    $active = $engine->getActivePlan($conversation);

    expect($active)->not->toBeNull()
        ->and($active->id)->toBe($plan->id);
});
