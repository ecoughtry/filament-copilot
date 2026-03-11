<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Events;

use EslamRedaDiv\FilamentCopilot\Models\CopilotConversation;
use Illuminate\Foundation\Events\Dispatchable;

class CopilotMessageSent
{
    use Dispatchable;

    public function __construct(
        public readonly CopilotConversation $conversation,
        public readonly string $content,
        public readonly string $panelId,
    ) {}
}
