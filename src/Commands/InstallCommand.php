<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\pause;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class InstallCommand extends Command
{
    protected $signature = 'filament-copilot:install
                            {--force : Overwrite existing config file}';

    protected $description = 'Install the Filament Copilot package.';

    protected const GITHUB_URL = 'https://github.com/eslam-reda-div/filament-copilot';

    public function handle(): int
    {
        info('🚀 Welcome to Filament Copilot Installer');
        note('This wizard will guide you through setting up the AI-powered copilot for your Filament panels.');

        pause('Press ENTER to begin the installation...');

        // ─── Step 1: Publish config ──────────────────────────────────
        info('Step 1/7 — Publishing configuration...');

        spin(
            callback: fn () => $this->callSilently('vendor:publish', [
                '--tag' => 'filament-copilot-config',
                '--force' => $this->option('force'),
            ]),
            message: 'Publishing config file...',
        );

        info('✓ Config file published to config/filament-copilot.php');

        // ─── Step 2: Publish & run migrations ────────────────────────
        info('Step 2/7 — Database setup...');

        spin(
            callback: fn () => $this->callSilently('vendor:publish', [
                '--tag' => 'filament-copilot-migrations',
            ]),
            message: 'Publishing migration files...',
        );

        info('✓ Migration files published.');

        $runMigrations = confirm(
            label: 'Would you like to run database migrations now?',
            default: true,
            hint: 'This will create the tables needed for conversations, audit logs, rate limits, etc.',
        );

        if ($runMigrations) {
            spin(
                callback: fn () => $this->callSilently('migrate'),
                message: 'Running database migrations...',
            );
            info('✓ Migrations completed successfully.');
        } else {
            warning('⚠ Remember to run "php artisan migrate" before using Filament Copilot.');
        }

        // ─── Step 3: Laravel AI SDK setup ────────────────────────────
        info('Step 3/7 — Laravel AI SDK Configuration...');
        note('Filament Copilot uses the official laravel/ai SDK. Let\'s configure your AI provider.');

        $publishAiConfig = confirm(
            label: 'Would you like to publish the laravel/ai config file (config/ai.php)?',
            default: true,
            hint: 'This is where provider API keys and default models are configured. Filament Copilot uses its own database tables — the AI SDK migrations are not needed.',
        );

        if ($publishAiConfig) {
            spin(
                callback: fn () => $this->callSilently('vendor:publish', [
                    '--tag' => 'ai-config',
                ]),
                message: 'Publishing laravel/ai config...',
            );
            info('✓ Laravel AI SDK config published to config/ai.php.');
        }

        // ─── Step 4: Select AI provider ──────────────────────────────
        info('Step 4/7 — Choose your AI provider...');

        $provider = select(
            label: 'Which AI provider would you like to use?',
            options: [
                'openai' => 'OpenAI (GPT-4o, GPT-4o-mini, o3, ...)',
                'anthropic' => 'Anthropic (Claude Sonnet, Opus, Haiku, ...)',
                'gemini' => 'Google Gemini (Gemini 2.0 Flash, Pro, ...)',
                'groq' => 'Groq (LLaMA, Mixtral — fast inference)',
                'xai' => 'xAI (Grok)',
                'deepseek' => 'DeepSeek (DeepSeek-V3, R1, ...)',
                'mistral' => 'Mistral (Mistral Large, Codestral, ...)',
                'ollama' => 'Ollama (local models — no API key needed)',
            ],
            default: 'openai',
            hint: 'You can change this later in config/filament-copilot.php',
        );

        $defaultModels = [
            'openai' => 'gpt-4o',
            'anthropic' => 'claude-sonnet-4-20250514',
            'gemini' => 'gemini-2.0-flash',
            'groq' => 'llama-3.3-70b-versatile',
            'xai' => 'grok-3',
            'deepseek' => 'deepseek-chat',
            'mistral' => 'mistral-large-latest',
            'ollama' => 'llama3',
        ];

        $modelSuggestions = [
            'openai' => ['gpt-4o', 'gpt-4o-mini', 'o3', 'o4-mini'],
            'anthropic' => ['claude-sonnet-4-20250514', 'claude-opus-4-20250514', 'claude-haiku-4-5-20251001'],
            'gemini' => ['gemini-2.0-flash', 'gemini-2.5-pro', 'gemini-2.5-flash'],
            'groq' => ['llama-3.3-70b-versatile', 'mixtral-8x7b-32768'],
            'xai' => ['grok-3', 'grok-3-mini'],
            'deepseek' => ['deepseek-chat', 'deepseek-reasoner'],
            'mistral' => ['mistral-large-latest', 'codestral-latest'],
            'ollama' => ['llama3', 'mistral', 'codellama', 'phi3'],
        ];

        // ─── Step 5: Select model ────────────────────────────────────
        info('Step 5/7 — Choose your AI model...');

        note('Popular models for '.$provider.': '.implode(', ', $modelSuggestions[$provider] ?? []));

        $model = text(
            label: 'Which model would you like to use?',
            placeholder: $defaultModels[$provider] ?? 'model-name',
            default: $defaultModels[$provider] ?? '',
            hint: 'You can type any model name supported by your provider.',
            required: 'A model name is required.',
        );

        // ─── Step 6: API key setup ───────────────────────────────────
        info('Step 6/7 — API key configuration...');

        $envKeyMap = [
            'openai' => 'OPENAI_API_KEY',
            'anthropic' => 'ANTHROPIC_API_KEY',
            'gemini' => 'GEMINI_API_KEY',
            'groq' => 'GROQ_API_KEY',
            'xai' => 'XAI_API_KEY',
            'deepseek' => 'DEEPSEEK_API_KEY',
            'mistral' => 'MISTRAL_API_KEY',
            'ollama' => null,
        ];

        $envKey = $envKeyMap[$provider] ?? null;

        if ($provider === 'ollama') {
            info('✓ Ollama runs locally — no API key needed.');
            note('Make sure Ollama is running: ollama serve');
        } else {
            $apiKey = text(
                label: "Enter your {$provider} API key",
                placeholder: 'sk-...',
                hint: "This will be saved to your .env file as {$envKey}. Leave empty to set it later.",
            );

            if (! empty($apiKey)) {
                $this->addEnvVariable((string) $envKey, $apiKey);
                info("✓ {$envKey} added to your .env file.");
            } else {
                warning("⚠ Don't forget to add {$envKey}=your-key to your .env file.");
            }

            // Also set COPILOT_PROVIDER and COPILOT_MODEL in .env
            $this->addEnvVariable('COPILOT_PROVIDER', $provider);
            $this->addEnvVariable('COPILOT_MODEL', $model);
        }

        // Update config defaults
        $configPath = config_path('filament-copilot.php');
        if (file_exists($configPath)) {
            $config = file_get_contents($configPath);

            $config = preg_replace(
                "/('provider'\s*=>\s*env\('COPILOT_PROVIDER',\s*')([^']*)('\))/",
                "'provider' => env('COPILOT_PROVIDER', '{$provider}')",
                $config
            );

            file_put_contents($configPath, $config);
        }

        // ─── Step 7: Summary & next steps ────────────────────────────
        info('Step 7/7 — Setup complete!');

        table(
            headers: ['Setting', 'Value'],
            rows: [
                ['AI Provider', $provider],
                ['AI Model', $model],
                ['API Key', $envKey ? (! empty($apiKey ?? '') ? '✓ Configured' : '⚠ Not set yet') : 'Not needed'],
                ['Config File', 'config/filament-copilot.php'],
                ['Migrations', $runMigrations ? '✓ Executed' : '⚠ Pending'],
            ],
        );

        note('Next steps to complete your setup:');

        info('1. Register the plugin in your Filament panel provider:');
        note(<<<'CODE'
        use EslamRedaDiv\FilamentCopilot\FilamentCopilotPlugin;

        public function panel(Panel $panel): Panel
        {
            return $panel
                // ...
                ->plugin(FilamentCopilotPlugin::make());
        }
        CODE);

        info('2. Add traits to your Resources, Pages, and Widgets:');
        table(
            headers: ['Trait', 'Use On', 'Purpose'],
            rows: [
                ['HasCopilotContext', 'Resource classes', 'Expose resources to the AI agent'],
                ['HasCopilotPageContext', 'Custom Page classes', 'Expose pages to the AI agent'],
                ['HasCopilotWidgetContext', 'Widget classes', 'Expose widgets to the AI agent'],
            ],
        );

        info('3. Use AI macros on your Filament components:');
        table(
            headers: ['Macro', 'Component', 'Example'],
            rows: [
                ['aiCanFill()', 'Form fields', "TextInput::make('name')->aiCanFill()"],
                ['aiCanRead()', 'Table columns', "TextColumn::make('name')->aiCanRead()"],
                ['aiCanFilter()', 'Table filters', "SelectFilter::make('status')->aiCanFilter()"],
                ['aiCanExecute()', 'Actions', "Action::make('approve')->aiCanExecute()"],
                ['aiCanInteract()', 'Widgets', '$this->aiCanInteract()'],
            ],
        );

        info('4. Optional — enable the management dashboard:');
        note(<<<'CODE'
        ->plugin(
            FilamentCopilotPlugin::make()
                ->managementEnabled()
                ->chatEnabled()
        )
        CODE);

        outro('🎉 Filament Copilot is ready! Your AI-powered admin panel awaits.');

        // ─── Star the repo ───────────────────────────────────────────
        $starRepo = confirm(
            label: 'Would you like to support the project by starring it on GitHub?',
            default: true,
            hint: 'This helps other developers discover Filament Copilot!',
        );

        if ($starRepo) {
            $this->openUrl(self::GITHUB_URL);
            info('Thank you for your support! ⭐');
        }

        return self::SUCCESS;
    }

    /**
     * Add or update an environment variable in the .env file.
     */
    protected function addEnvVariable(string $key, string $value): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);

        // Check if the key already exists
        if (preg_match("/^{$key}=/m", $envContent)) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            $envContent .= PHP_EOL."{$key}={$value}";
        }

        file_put_contents($envPath, $envContent);
    }

    /**
     * Open a URL in the user's default browser.
     */
    protected function openUrl(string $url): void
    {
        $command = match (PHP_OS_FAMILY) {
            'Darwin' => "open \"{$url}\"",
            'Windows' => "start \"{$url}\"",
            default => "xdg-open \"{$url}\"",
        };

        exec($command);
    }
}
