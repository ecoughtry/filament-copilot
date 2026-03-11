<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Contracts;

interface DescribesForCopilot
{
    /**
     * Provide a human-readable description of this component for the AI agent.
     */
    public function copilotDescription(): string;
}
