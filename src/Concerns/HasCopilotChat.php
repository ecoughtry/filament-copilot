<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Concerns;

use EslamRedaDiv\FilamentCopilot\Models\CopilotConversation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Add to User (or authenticatable) models to link them with copilot conversations.
 */
trait HasCopilotChat
{
    public function copilotConversations(): MorphMany
    {
        return $this->morphMany(CopilotConversation::class, 'participant');
    }
}
