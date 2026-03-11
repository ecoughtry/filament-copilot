<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Tools;

use EslamRedaDiv\FilamentCopilot\Events\CopilotToolExecuted;
use EslamRedaDiv\FilamentCopilot\Models\CopilotToolCall;
use EslamRedaDiv\FilamentCopilot\Tools\Concerns\LogsAudit;
use EslamRedaDiv\FilamentCopilot\Tools\Concerns\ValidatesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Laravel\Ai\Contracts\Tool;

abstract class BaseTool implements Tool
{
    use LogsAudit;
    use ValidatesAuthorization;

    protected string $panelId;

    protected Model $user;

    protected ?Model $tenant = null;

    public function forPanel(string $panelId): static
    {
        $this->panelId = $panelId;

        return $this;
    }

    public function forUser(Model $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function forTenant(?Model $tenant): static
    {
        $this->tenant = $tenant;

        return $this;
    }

    /**
     * Dispatch a CopilotToolExecuted event for this tool.
     */
    protected function dispatchToolExecuted(string $toolName, string $result, ?string $messageId = null, ?array $input = null): void
    {
        $toolCall = new CopilotToolCall([
            'message_id' => $messageId,
            'tool_name' => $toolName,
            'input' => $input ?? [],
            'output' => $result,
            'status' => 'completed',
        ]);

        event(new CopilotToolExecuted(
            toolCall: $toolCall,
            toolName: $toolName,
            result: $result,
        ));
    }
}
