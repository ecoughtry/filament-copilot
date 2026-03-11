<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Agent;

use EslamRedaDiv\FilamentCopilot\Agent\Middleware\AuditMiddleware;
use EslamRedaDiv\FilamentCopilot\Agent\Middleware\RateLimitMiddleware;
use Illuminate\Database\Eloquent\Model;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasMiddleware;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

class CopilotAgent implements Agent, Conversational, HasMiddleware, HasTools
{
    use Promptable;

    protected string $panelId;

    protected ?Model $tenant = null;

    protected Model $user;

    protected array $tools = [];

    protected iterable $conversationMessages = [];

    protected ?string $systemPrompt = null;

    protected bool $shouldThink = false;

    protected bool $shouldPlan = false;

    public function __construct(
        protected ContextBuilder $contextBuilder,
    ) {}

    public function forPanel(string $panelId): static
    {
        $this->panelId = $panelId;

        return $this;
    }

    public function forTenant(?Model $tenant): static
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function forUser(Model $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function withTools(array $tools): static
    {
        $this->tools = $tools;

        return $this;
    }

    public function withMessages(iterable $messages): static
    {
        $this->conversationMessages = $messages;

        return $this;
    }

    public function withSystemPrompt(string $prompt): static
    {
        $this->systemPrompt = $prompt;

        return $this;
    }

    public function thinking(bool $shouldThink = true): static
    {
        $this->shouldThink = $shouldThink;

        return $this;
    }

    public function planning(bool $shouldPlan = true): static
    {
        $this->shouldPlan = $shouldPlan;

        return $this;
    }

    public function instructions(): Stringable|string
    {
        return $this->contextBuilder
            ->forPanel($this->panelId)
            ->forTenant($this->tenant)
            ->forUser($this->user)
            ->withCustomPrompt($this->systemPrompt)
            ->withPlanning($this->shouldPlan)
            ->build();
    }

    public function tools(): iterable
    {
        return $this->tools;
    }

    public function messages(): iterable
    {
        return $this->conversationMessages;
    }

    public function middleware(): array
    {
        return [
            new RateLimitMiddleware($this->panelId, $this->user, $this->tenant),
            new AuditMiddleware($this->panelId, $this->user, $this->tenant),
        ];
    }

    public function getPanelId(): string
    {
        return $this->panelId;
    }

    public function getUser(): Model
    {
        return $this->user;
    }

    public function getTenant(): ?Model
    {
        return $this->tenant;
    }
}
