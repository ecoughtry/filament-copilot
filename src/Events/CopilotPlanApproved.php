<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Events;

use EslamRedaDiv\FilamentCopilot\Models\CopilotConversation;
use EslamRedaDiv\FilamentCopilot\Models\CopilotPlan;
use Illuminate\Foundation\Events\Dispatchable;

class CopilotPlanApproved
{
    use Dispatchable;

    public function __construct(
        public readonly CopilotPlan $plan,
        public readonly CopilotConversation $conversation,
    ) {}
}
