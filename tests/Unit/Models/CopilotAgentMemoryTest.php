<?php

use EslamRedaDiv\FilamentCopilot\Models\CopilotAgentMemory;

it('can create a memory', function () {
    $user = createTestUser();

    $memory = CopilotAgentMemory::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'key' => 'user_preference',
        'value' => 'Prefers dark mode',
    ]);

    expect($memory)->toBeInstanceOf(CopilotAgentMemory::class)
        ->and($memory->key)->toBe('user_preference')
        ->and($memory->value)->toBe('Prefers dark mode');
});

it('can retrieve memories by participant', function () {
    $user = createTestUser();

    CopilotAgentMemory::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'key' => 'pref1',
        'value' => 'Value 1',
    ]);

    CopilotAgentMemory::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'key' => 'pref2',
        'value' => 'Value 2',
    ]);

    $memories = CopilotAgentMemory::where('participant_type', $user->getMorphClass())
        ->where('participant_id', $user->getKey())
        ->get();

    expect($memories)->toHaveCount(2);
});
