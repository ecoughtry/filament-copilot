<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot;

use EslamRedaDiv\FilamentCopilot\Commands\InstallCommand;
use EslamRedaDiv\FilamentCopilot\Macros\MacroRegistrar;
use EslamRedaDiv\FilamentCopilot\Services\ConversationManager;
use EslamRedaDiv\FilamentCopilot\Services\ExportService;
use EslamRedaDiv\FilamentCopilot\Services\RateLimitService;
use EslamRedaDiv\FilamentCopilot\Services\ToolRegistry;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentCopilotServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-copilot';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasConfigFile()
            ->hasRoute('web')
            ->hasMigrations([
                'create_copilot_conversations_table',
                'create_copilot_messages_table',
                'create_copilot_tool_calls_table',
                'create_copilot_plans_table',
                'create_copilot_audit_logs_table',
                'create_copilot_rate_limits_table',
                'create_copilot_token_usages_table',
                'create_copilot_agent_memories_table',
            ])
            ->hasCommand(InstallCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(RateLimitService::class);
        $this->app->singleton(ConversationManager::class);
        $this->app->singleton(ToolRegistry::class);
        $this->app->singleton(ExportService::class);
    }

    public function packageBooted(): void
    {
        (new MacroRegistrar)->register();
    }
}
