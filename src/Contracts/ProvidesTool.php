<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Contracts;

use Laravel\Ai\Contracts\Tool;

interface ProvidesTool
{
    /**
     * Return custom tools to register with the Copilot agent.
     *
     * @return array<Tool>
     */
    public function copilotTools(): array;
}
