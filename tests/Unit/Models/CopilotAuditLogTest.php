<?php

use EslamRedaDiv\FilamentCopilot\Enums\AuditAction;
use EslamRedaDiv\FilamentCopilot\Models\CopilotAuditLog;

it('can log an audit action', function () {
    $user = createTestUser();
    $log = CopilotAuditLog::log(
        action: AuditAction::MessageSent,
        participant: $user,
        panelId: 'admin',
        payload: ['message' => 'Hello'],
    );

    expect($log)->toBeInstanceOf(CopilotAuditLog::class)
        ->and($log->action)->toBe(AuditAction::MessageSent)
        ->and($log->panel_id)->toBe('admin')
        ->and($log->payload)->toBeArray()
        ->and($log->payload['message'])->toBe('Hello');
});

it('casts action to enum', function () {
    $user = createTestUser();
    $log = CopilotAuditLog::log(
        action: AuditAction::RecordCreated,
        participant: $user,
        panelId: 'admin',
        resourceType: 'users',
        recordKey: '1',
    );

    expect($log->action)->toBeInstanceOf(AuditAction::class)
        ->and($log->action->value)->toBe('record_created');
});

it('scopes by action', function () {
    $user = createTestUser();
    CopilotAuditLog::log(AuditAction::MessageSent, $user, 'admin');
    CopilotAuditLog::log(AuditAction::RecordRead, $user, 'admin');
    CopilotAuditLog::log(AuditAction::MessageSent, $user, 'admin');

    expect(CopilotAuditLog::forAction(AuditAction::MessageSent)->count())->toBe(2)
        ->and(CopilotAuditLog::forAction(AuditAction::RecordRead)->count())->toBe(1);
});

it('scopes by panel', function () {
    $user = createTestUser();
    CopilotAuditLog::log(AuditAction::MessageSent, $user, 'admin');
    CopilotAuditLog::log(AuditAction::MessageSent, $user, 'app');

    expect(CopilotAuditLog::forPanel('admin')->count())->toBe(1);
});

it('scopes by participant', function () {
    $user = createTestUser();
    CopilotAuditLog::log(AuditAction::MessageSent, $user, 'admin');

    expect(CopilotAuditLog::forParticipant($user)->count())->toBe(1);
});
