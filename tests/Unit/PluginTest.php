<?php

use EslamRedaDiv\FilamentCopilot\FilamentCopilotPlugin;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

it('creates plugin instance', function () {
    $plugin = FilamentCopilotPlugin::make();

    expect($plugin)->toBeInstanceOf(FilamentCopilotPlugin::class)
        ->and($plugin->getId())->toBe('filament-copilot');
});

it('configures chat enabled', function () {
    $plugin = FilamentCopilotPlugin::make()->chatEnabled(false);

    expect($plugin->isChatEnabled())->toBeFalse();
});

it('configures management', function () {
    $plugin = FilamentCopilotPlugin::make()
        ->managementEnabled()
        ->managementGuard('web');

    expect($plugin->isManagementEnabled())->toBeTrue()
        ->and($plugin->getManagementGuard())->toBe('web');
});

it('configures provider and model', function () {
    $plugin = FilamentCopilotPlugin::make()
        ->provider('anthropic')
        ->model('claude-sonnet-4-20250514');

    expect($plugin->getProvider())->toBe('anthropic')
        ->and($plugin->getModel())->toBe('claude-sonnet-4-20250514');
});

it('configures thinking and planning', function () {
    $plugin = FilamentCopilotPlugin::make()
        ->thinking()
        ->planning()
        ->shouldApprovePlan();

    expect($plugin->shouldThink())->toBeTrue()
        ->and($plugin->shouldPlan())->toBeTrue()
        ->and($plugin->requiresPlanApproval())->toBeTrue();
});

it('configures quick actions', function () {
    $actions = [
        ['label' => 'Help', 'prompt' => 'Help me'],
    ];

    $plugin = FilamentCopilotPlugin::make()->quickActions($actions);

    expect($plugin->getQuickActions())->toHaveCount(1)
        ->and($plugin->getQuickActions()[0]['label'])->toBe('Help');
});

it('configures global tools', function () {
    $tool = new class implements Tool
    {
        public function description(): string
        {
            return 'test';
        }

        public function schema(JsonSchema $schema): array
        {
            return [];
        }

        public function handle(Request $request): string
        {
            return 'ok';
        }
    };

    $plugin = FilamentCopilotPlugin::make()->globalTools([$tool]);

    expect($plugin->getGlobalTools())->toHaveCount(1);
});

it('configures authorize callback', function () {
    $plugin = FilamentCopilotPlugin::make()->authorizeUsing(fn () => true);

    expect($plugin->getAuthorizeUsing())->toBeCallable();
});
