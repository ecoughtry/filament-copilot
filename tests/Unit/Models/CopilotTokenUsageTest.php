<?php

use EslamRedaDiv\FilamentCopilot\Models\CopilotTokenUsage;

it('records token usage', function () {
    $user = createTestUser();
    $usage = CopilotTokenUsage::record(
        participant: $user,
        panelId: 'admin',
        inputTokens: 100,
        outputTokens: 200,
        model: 'gpt-4o',
        provider: 'openai',
    );

    expect($usage->input_tokens)->toBe(100)
        ->and($usage->output_tokens)->toBe(200)
        ->and($usage->total_tokens)->toBe(300)
        ->and($usage->model)->toBe('gpt-4o')
        ->and($usage->provider)->toBe('openai')
        ->and($usage->usage_date)->not->toBeNull();
});

it('scopes by today', function () {
    $user = createTestUser();
    CopilotTokenUsage::record($user, 'admin', 50, 100);

    expect(CopilotTokenUsage::forToday()->count())->toBe(1);
});

it('scopes by participant', function () {
    $user = createTestUser();
    CopilotTokenUsage::record($user, 'admin', 50, 100);

    expect(CopilotTokenUsage::forParticipant($user)->count())->toBe(1);
});

it('scopes by panel', function () {
    $user = createTestUser();
    CopilotTokenUsage::record($user, 'admin', 50, 100);
    CopilotTokenUsage::record($user, 'app', 50, 100);

    expect(CopilotTokenUsage::forPanel('admin')->count())->toBe(1)
        ->and(CopilotTokenUsage::forPanel('app')->count())->toBe(1);
});

it('correctly calculates total tokens', function () {
    $user = createTestUser();
    $usage = CopilotTokenUsage::record($user, 'admin', 500, 1500);

    expect($usage->total_tokens)->toBe(2000);
});
