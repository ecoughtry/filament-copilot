<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot;

use Closure;
use EslamRedaDiv\FilamentCopilot\Livewire\ConversationSidebar;
use EslamRedaDiv\FilamentCopilot\Livewire\CopilotButton;
use EslamRedaDiv\FilamentCopilot\Livewire\CopilotChat;
use EslamRedaDiv\FilamentCopilot\Pages\CopilotDashboardPage;
use EslamRedaDiv\FilamentCopilot\Resources\CopilotAuditLogs\CopilotAuditLogResource;
use EslamRedaDiv\FilamentCopilot\Resources\CopilotConversations\CopilotConversationResource;
use EslamRedaDiv\FilamentCopilot\Resources\CopilotRateLimits\CopilotRateLimitResource;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Laravel\Ai\Contracts\Tool;
use Livewire\Livewire;

class FilamentCopilotPlugin implements Plugin
{
    protected bool $chatEnabled = true;

    protected bool $managementEnabled = false;

    protected ?string $managementGuard = null;

    protected ?string $provider = null;

    protected ?string $model = null;

    protected bool $shouldThink = false;

    protected bool $shouldPlan = false;

    protected bool $shouldApprovePlan = false;

    /** @var array<Tool> */
    protected array $globalTools = [];

    protected array $quickActions = [];

    protected ?Closure $authorizeUsing = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function getId(): string
    {
        return 'filament-copilot';
    }

    public function chatEnabled(bool $enabled = true): static
    {
        $this->chatEnabled = $enabled;

        return $this;
    }

    public function isChatEnabled(): bool
    {
        return $this->chatEnabled && config('filament-copilot.chat.enabled', true);
    }

    public function managementEnabled(bool $enabled = true): static
    {
        $this->managementEnabled = $enabled;

        return $this;
    }

    public function isManagementEnabled(): bool
    {
        return $this->managementEnabled;
    }

    public function managementGuard(?string $guard): static
    {
        $this->managementGuard = $guard;

        return $this;
    }

    public function getManagementGuard(): ?string
    {
        return $this->managementGuard ?? config('filament-copilot.management.guard');
    }

    public function provider(string $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider ?? config('filament-copilot.provider', 'openai');
    }

    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model ?? config('filament-copilot.model');
    }

    public function thinking(bool $shouldThink = true): static
    {
        $this->shouldThink = $shouldThink;

        return $this;
    }

    public function shouldThink(): bool
    {
        return $this->shouldThink || config('filament-copilot.agent.should_think', false);
    }

    public function planning(bool $shouldPlan = true): static
    {
        $this->shouldPlan = $shouldPlan;

        return $this;
    }

    public function shouldPlan(): bool
    {
        return $this->shouldPlan || config('filament-copilot.agent.should_plan', false);
    }

    public function shouldApprovePlan(bool $shouldApprove = true): static
    {
        $this->shouldApprovePlan = $shouldApprove;

        return $this;
    }

    public function requiresPlanApproval(): bool
    {
        return $this->shouldApprovePlan || config('filament-copilot.agent.should_approve_plan', false);
    }

    public function globalTools(array $tools): static
    {
        $this->globalTools = $tools;

        return $this;
    }

    public function getGlobalTools(): array
    {
        return $this->globalTools;
    }

    public function quickActions(array $actions): static
    {
        $this->quickActions = $actions;

        return $this;
    }

    public function getQuickActions(): array
    {
        return ! empty($this->quickActions) ? $this->quickActions : config('filament-copilot.quick_actions', []);
    }

    public function authorizeUsing(?Closure $callback): static
    {
        $this->authorizeUsing = $callback;

        return $this;
    }

    public function getAuthorizeUsing(): ?Closure
    {
        return $this->authorizeUsing;
    }

    public function register(Panel $panel): void
    {
        if ($this->managementEnabled) {
            $panel->resources([
                CopilotConversationResource::class,
                CopilotAuditLogResource::class,
                CopilotRateLimitResource::class,
            ]);

            $panel->pages([
                CopilotDashboardPage::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        Livewire::component('filament-copilot-chat', CopilotChat::class);
        Livewire::component('filament-copilot-button', CopilotButton::class);
        Livewire::component('filament-copilot-sidebar', ConversationSidebar::class);

        if ($this->isChatEnabled()) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('@livewire(\'filament-copilot-chat\') @livewire(\'filament-copilot-button\')'),
            );
        }
    }
}
