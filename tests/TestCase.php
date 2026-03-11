<?php

namespace EslamRedaDiv\FilamentCopilot\Tests;

use EslamRedaDiv\FilamentCopilot\FilamentCopilotServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Model::unguard();

        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            FilamentCopilotServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('filament-copilot.provider', 'openai');
        $app['config']->set('filament-copilot.model', 'gpt-4o');
    }
}
