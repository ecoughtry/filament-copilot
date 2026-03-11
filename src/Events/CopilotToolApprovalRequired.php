<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Events;

use EslamRedaDiv\FilamentCopilot\Models\CopilotToolCall;
use Illuminate\Foundation\Events\Dispatchable;

class CopilotToolApprovalRequired
{
    use Dispatchable;

    public function __construct(
        public readonly CopilotToolCall $toolCall,
    ) {}
}
