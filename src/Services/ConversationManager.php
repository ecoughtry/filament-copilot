<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Services;

use EslamRedaDiv\FilamentCopilot\Enums\MessageRole;
use EslamRedaDiv\FilamentCopilot\Events\CopilotConversationCreated;
use EslamRedaDiv\FilamentCopilot\Models\CopilotConversation;
use EslamRedaDiv\FilamentCopilot\Models\CopilotMessage;
use Illuminate\Database\Eloquent\Model;

class ConversationManager
{
    /**
     * Start a new conversation.
     */
    public function create(
        Model $user,
        string $panelId,
        ?Model $tenant = null,
        ?string $title = null,
    ): CopilotConversation {
        $conversation = CopilotConversation::create([
            'participant_type' => $user->getMorphClass(),
            'participant_id' => $user->getKey(),
            'panel_id' => $panelId,
            'tenant_type' => $tenant?->getMorphClass(),
            'tenant_id' => $tenant?->getKey(),
            'title' => $title ?? 'New Conversation',
        ]);

        event(new CopilotConversationCreated($conversation));

        return $conversation;
    }

    /**
     * Add a user message to a conversation.
     */
    public function addUserMessage(CopilotConversation $conversation, string $content): CopilotMessage
    {
        return $conversation->messages()->create([
            'role' => MessageRole::User,
            'content' => $content,
        ]);
    }

    /**
     * Add an assistant message to a conversation.
     */
    public function addAssistantMessage(
        CopilotConversation $conversation,
        string $content,
        int $inputTokens = 0,
        int $outputTokens = 0,
        ?array $metadata = null,
    ): CopilotMessage {
        return $conversation->messages()->create([
            'role' => MessageRole::Assistant,
            'content' => $content,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get conversations for a user in a panel.
     */
    public function getConversations(
        Model $user,
        string $panelId,
        ?Model $tenant = null,
        int $limit = 20,
    ) {
        return CopilotConversation::query()
            ->forPanel($panelId)
            ->forParticipant($user)
            ->forTenant($tenant)
            ->with('latestMessage')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get messages for a conversation in the format expected by the AI SDK.
     */
    public function getMessagesForAgent(CopilotConversation $conversation, ?int $limit = null): array
    {
        $limit ??= config('filament-copilot.chat.max_conversation_messages', 50);

        return $conversation->messages()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn (CopilotMessage $message) => [
                'role' => $message->role->value,
                'content' => $message->content,
            ])
            ->values()
            ->toArray();
    }

    /**
     * Delete a conversation and all related data.
     */
    public function delete(CopilotConversation $conversation): void
    {
        $conversation->delete();
    }

    /**
     * Update a conversation title.
     */
    public function updateTitle(CopilotConversation $conversation, string $title): void
    {
        $conversation->update(['title' => $title]);
    }
}
