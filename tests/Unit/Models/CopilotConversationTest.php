<?php

use EslamRedaDiv\FilamentCopilot\Enums\MessageRole;
use EslamRedaDiv\FilamentCopilot\Models\CopilotConversation;
use EslamRedaDiv\FilamentCopilot\Models\CopilotMessage;

it('can create a conversation', function () {
    $user = createTestUser();
    $conversation = CopilotConversation::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'title' => 'Test Conversation',
    ]);

    expect($conversation)->toBeInstanceOf(CopilotConversation::class)
        ->and($conversation->title)->toBe('Test Conversation')
        ->and($conversation->panel_id)->toBe('admin')
        ->and($conversation->id)->not->toBeNull();
});

it('has messages relationship', function () {
    $user = createTestUser();
    $conversation = CopilotConversation::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
    ]);

    CopilotMessage::create([
        'conversation_id' => $conversation->id,
        'role' => MessageRole::User,
        'content' => 'Hello',
    ]);

    expect($conversation->messages)->toHaveCount(1)
        ->and($conversation->messages->first()->content)->toBe('Hello');
});

it('scopes by panel', function () {
    $user = createTestUser();
    CopilotConversation::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
    ]);

    CopilotConversation::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'app',
    ]);

    expect(CopilotConversation::forPanel('admin')->count())->toBe(1)
        ->and(CopilotConversation::forPanel('app')->count())->toBe(1);
});

it('scopes by participant', function () {
    $user = createTestUser();
    CopilotConversation::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
    ]);

    expect(CopilotConversation::forParticipant($user)->count())->toBe(1);
});

it('casts metadata to array', function () {
    $user = createTestUser();
    $conversation = CopilotConversation::create([
        'participant_type' => $user->getMorphClass(),
        'participant_id' => $user->getKey(),
        'panel_id' => 'admin',
        'metadata' => ['key' => 'value'],
    ]);

    expect($conversation->metadata)->toBeArray()
        ->and($conversation->metadata['key'])->toBe('value');
});
