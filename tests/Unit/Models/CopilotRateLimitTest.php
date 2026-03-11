<?php

use EslamRedaDiv\FilamentCopilot\Models\CopilotRateLimit;

it('can create rate limit record', function () {
    $user = createTestUser();
    $limit = CopilotRateLimit::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'max_messages_per_hour' => 30,
        'max_messages_per_day' => 300,
        'copilot_enabled' => true,
    ]);

    expect($limit->max_messages_per_hour)->toBe(30)
        ->and($limit->max_messages_per_day)->toBe(300)
        ->and($limit->copilot_enabled)->toBeTrue();
});

it('can block and unblock', function () {
    $user = createTestUser();
    $limit = CopilotRateLimit::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'copilot_enabled' => true,
    ]);

    $limit->block('Testing');
    $limit->refresh();

    expect($limit->is_blocked)->toBeTrue()
        ->and($limit->blocked_reason)->toBe('Testing');

    $limit->unblock();
    $limit->refresh();

    expect($limit->is_blocked)->toBeFalse()
        ->and($limit->blocked_reason)->toBeNull();
});

it('checks if currently blocked with expiry', function () {
    $user = createTestUser();
    $limit = CopilotRateLimit::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'copilot_enabled' => true,
        'is_blocked' => true,
        'blocked_until' => now()->subMinute(), // Already expired
    ]);

    expect($limit->isCurrentlyBlocked())->toBeFalse();
});

it('checks if currently blocked without expiry', function () {
    $user = createTestUser();
    $limit = CopilotRateLimit::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'copilot_enabled' => true,
        'is_blocked' => true,
        'blocked_until' => now()->addHour(),
    ]);

    expect($limit->isCurrentlyBlocked())->toBeTrue();
});

it('can enable and disable copilot', function () {
    $user = createTestUser();
    $limit = CopilotRateLimit::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'copilot_enabled' => true,
    ]);

    $limit->disableCopilot();
    $limit->refresh();
    expect($limit->copilot_enabled)->toBeFalse();

    $limit->enableCopilot();
    $limit->refresh();
    expect($limit->copilot_enabled)->toBeTrue();
});

it('scopes by panel and participant', function () {
    $user = createTestUser();
    CopilotRateLimit::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'copilot_enabled' => true,
    ]);

    expect(CopilotRateLimit::forPanel('admin')->forParticipant($user)->count())->toBe(1)
        ->and(CopilotRateLimit::forPanel('other')->count())->toBe(0);
});
