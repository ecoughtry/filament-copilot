<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Tools\Concerns;

use EslamRedaDiv\FilamentCopilot\Agent\Middleware\AuditMiddleware;
use EslamRedaDiv\FilamentCopilot\Enums\AuditAction;

trait LogsAudit
{
    protected function audit(
        AuditAction $action,
        ?string $resourceType = null,
        ?string $recordKey = null,
        ?array $payload = null,
    ): void {
        if (! config('filament-copilot.audit.enabled', true)) {
            return;
        }

        AuditMiddleware::logAction(
            action: $action,
            user: $this->user,
            panelId: $this->panelId,
            tenant: $this->tenant,
            resourceType: $resourceType,
            recordKey: $recordKey,
            payload: $payload,
        );
    }
}
