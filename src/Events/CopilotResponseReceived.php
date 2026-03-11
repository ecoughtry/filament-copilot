<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Events;

use EslamRedaDiv\FilamentCopilot\Models\CopilotConversation;
use EslamRedaDiv\FilamentCopilot\Models\CopilotMessage;
use Illuminate\Foundation\Events\Dispatchable;

class CopilotResponseReceived
{
    use Dispatchable;

    public function __construct(
        public readonly CopilotConversation $conversation,
        public readonly CopilotMessage $message,
        public readonly int $inputTokens,
        public readonly int $outputTokens,
    ) {}
}
