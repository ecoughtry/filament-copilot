<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Tools;

use EslamRedaDiv\FilamentCopilot\Services\ExportService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

class ExportConversationTool extends BaseTool
{
    public function __construct(
        protected ExportService $exportService,
    ) {}

    public function description(): Stringable|string
    {
        return 'Export the current conversation to Markdown format.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'conversation_id' => $schema->string()->description('The conversation ID to export')->required(),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        if (! config('filament-copilot.export.enabled', true)) {
            return 'Export feature is disabled.';
        }

        $conversationId = $request->get('conversation_id');
        $content = $this->exportService->toMarkdown($conversationId);

        if ($content === null) {
            return 'Conversation not found.';
        }

        return "Conversation exported as Markdown:\n\n{$content}";
    }
}
