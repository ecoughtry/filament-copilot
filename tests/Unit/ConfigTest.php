<?php

it('loads the config file', function () {
    expect(config('filament-copilot'))->toBeArray();
});

it('has default provider set', function () {
    expect(config('filament-copilot.provider'))->toBe('openai');
});

it('has agent configuration', function () {
    expect(config('filament-copilot.agent'))->toBeArray()
        ->and(config('filament-copilot.agent.should_think'))->toBeFalse()
        ->and(config('filament-copilot.agent.should_plan'))->toBeFalse()
        ->and(config('filament-copilot.agent.max_steps'))->toBe(10)
        ->and(config('filament-copilot.agent.temperature'))->toBe(0.3)
        ->and(config('filament-copilot.agent.max_tokens'))->toBe(4096);
});

it('has chat configuration', function () {
    expect(config('filament-copilot.chat.enabled'))->toBeTrue()
        ->and(config('filament-copilot.chat.max_conversation_messages'))->toBe(50);
});

it('has rate limit configuration', function () {
    expect(config('filament-copilot.rate_limits.enabled'))->toBeFalse()
        ->and(config('filament-copilot.rate_limits.max_messages_per_hour'))->toBe(60)
        ->and(config('filament-copilot.rate_limits.max_messages_per_day'))->toBe(500);
});

it('has audit configuration', function () {
    expect(config('filament-copilot.audit.enabled'))->toBeTrue()
        ->and(config('filament-copilot.audit.log_messages'))->toBeTrue()
        ->and(config('filament-copilot.audit.log_tool_calls'))->toBeTrue();
});

it('has memory configuration', function () {
    expect(config('filament-copilot.memory.enabled'))->toBeTrue()
        ->and(config('filament-copilot.memory.max_memories_per_user'))->toBe(100);
});

it('respects authorization by default', function () {
    expect(config('filament-copilot.respect_authorization'))->toBeTrue();
});

it('has export configuration', function () {
    expect(config('filament-copilot.export.enabled'))->toBeTrue()
        ->and(config('filament-copilot.export.formats'))->toContain('markdown');
});
