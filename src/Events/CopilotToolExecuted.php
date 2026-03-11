<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Events;

use EslamRedaDiv\FilamentCopilot\Models\CopilotToolCall;
use Illuminate\Foundation\Events\Dispatchable;

class CopilotToolExecuted
{
    use Dispatchable;

    public function __construct(
        public readonly CopilotToolCall $toolCall,
        public readonly string $toolName,
        public readonly string $result,
    ) {}
}
