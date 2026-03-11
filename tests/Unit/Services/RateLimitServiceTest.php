<?php

use EslamRedaDiv\FilamentCopilot\Models\CopilotConversation;
use EslamRedaDiv\FilamentCopilot\Models\CopilotMessage;
use EslamRedaDiv\FilamentCopilot\Models\CopilotRateLimit;
use EslamRedaDiv\FilamentCopilot\Models\CopilotTokenUsage;
use EslamRedaDiv\FilamentCopilot\Services\RateLimitService;

beforeEach(function () {
    config()->set('filament-copilot.rate_limits.enabled', true);
    config()->set('filament-copilot.rate_limits.max_messages_per_hour', 5);
    config()->set('filament-copilot.rate_limits.max_messages_per_day', 10);
});

it('allows messages under default limit', function () {
    $user = createTestUser();
    $service = app(RateLimitService::class);

    expect($service->canSendMessage($user, 'admin'))->toBeTrue();
});

it('blocks when user is blocked', function () {
    $user = createTestUser();
    $service = app(RateLimitService::class);

    CopilotRateLimit::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'copilot_enabled' => true,
        'is_blocked' => true,
        'blocked_until' => now()->addHour(),
    ]);

    expect($service->canSendMessage($user, 'admin'))->toBeFalse();
});

it('blocks when copilot is disabled', function () {
    $user = createTestUser();
    $service = app(RateLimitService::class);

    CopilotRateLimit::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'copilot_enabled' => false,
    ]);

    expect($service->canSendMessage($user, 'admin'))->toBeFalse();
});

it('denies when hourly message limit reached', function () {
    $user = createTestUser();
    $service = app(RateLimitService::class);

    $conversation = CopilotConversation::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
    ]);

    // Create 5 messages (the limit)
    for ($i = 0; $i < 5; $i++) {
        CopilotMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => "Message $i",
        ]);
    }

    expect($service->canSendMessage($user, 'admin'))->toBeFalse();
});

it('records token usage', function () {
    $user = createTestUser();
    $service = app(RateLimitService::class);

    $service->recordTokenUsage(
        user: $user,
        panelId: 'admin',
        inputTokens: 100,
        outputTokens: 200,
        model: 'gpt-4o',
        provider: 'openai',
    );

    expect(CopilotTokenUsage::count())->toBe(1);
});

it('gets remaining messages', function () {
    $user = createTestUser();
    $service = app(RateLimitService::class);

    $remaining = $service->getRemainingMessages($user, 'admin');

    expect($remaining)->toBe(5);
});

it('blocks and unblocks user', function () {
    $user = createTestUser();
    $service = app(RateLimitService::class);

    $service->blockUser($user, 'admin', 'Testing');
    expect($service->canSendMessage($user, 'admin'))->toBeFalse();

    $service->unblockUser($user, 'admin');
    expect($service->canSendMessage($user, 'admin'))->toBeTrue();
});
