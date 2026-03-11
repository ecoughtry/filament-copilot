<div x-data="{
    open: @entangle('isOpen'),
    showHistory: @entangle('showHistory'),
    streamingEnabled: @entangle('streamingEnabled'),
    isStreaming: false,
    streamedContent: '',
    init() {
        this.$watch('open', value => {
            if (value) {
                this.$nextTick(() => this.scrollToBottom());
            }
        });

        Livewire.hook('morph.updated', ({ el }) => {
            if (el.id === 'copilot-messages') {
                this.scrollToBottom();
            }
        });

        Livewire.on('copilot-send-stream', (data) => {
            this.startStreaming(data[0] || data);
        });
    },
    scrollToBottom() {
        const container = this.$refs.messages;
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    },
    handleKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $wire.sendMessage();
        }
    },
    async startStreaming(params) {
        this.isStreaming = true;
        this.streamedContent = '';

        try {
            const response = await fetch(params.streamUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'text/event-stream',
                    'X-CSRF-TOKEN': params.csrfToken,
                },
                body: JSON.stringify({
                    message: params.message,
                    conversation_id: params.conversationId,
                    panel_id: params.panelId,
                }),
            });

            if (!response.ok) {
                throw new Error('Stream request failed: ' + response.status);
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';
            let newConversationId = null;

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop() || '';

                let currentEvent = null;

                for (const line of lines) {
                    if (line.startsWith('event: ')) {
                        currentEvent = line.substring(7).trim();
                    } else if (line.startsWith('data: ') && currentEvent) {
                        try {
                            const data = JSON.parse(line.substring(6));

                            switch (currentEvent) {
                                case 'conversation':
                                    newConversationId = data.id;
                                    break;
                                case 'chunk':
                                    this.streamedContent += data.text;
                                    this.$nextTick(() => this.scrollToBottom());
                                    break;
                                case 'error':
                                    $wire.dispatch('copilot-stream-error', { error: data.message });
                                    break;
                                case 'done':
                                    break;
                            }
                        } catch (e) {
                            // Skip malformed JSON
                        }

                        currentEvent = null;
                    }
                }
            }

            // Notify Livewire that streaming is complete
            if (this.streamedContent) {
                $wire.dispatch('copilot-stream-complete', {
                    content: this.streamedContent,
                    newConversationId: newConversationId,
                });
            }
        } catch (error) {
            $wire.dispatch('copilot-stream-error', { error: error.message });
        } finally {
            this.isStreaming = false;
            this.streamedContent = '';
        }
    }
}" x-show="open" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4" x-cloak @copilot-open.window="open = true"
    class="fixed bottom-20 right-6 z-50 flex flex-col w-[420px] h-[600px] max-h-[80vh] bg-white dark:bg-gray-900 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 bg-primary-600 dark:bg-primary-700 text-white shrink-0">
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-o-sparkles" class="w-5 h-5" />
            <span class="font-semibold text-sm">{{ __('filament-copilot::filament-copilot.title') }}</span>
        </div>
        <div class="flex items-center gap-1">
            <button wire:click="toggleHistory" type="button" class="p-1.5 rounded-lg hover:bg-white/20 transition"
                title="{{ __('filament-copilot::filament-copilot.history') }}">
                <x-filament::icon icon="heroicon-o-clock" class="w-4 h-4" />
            </button>
            <button wire:click="newConversation" type="button" class="p-1.5 rounded-lg hover:bg-white/20 transition"
                title="{{ __('filament-copilot::filament-copilot.new_conversation') }}">
                <x-filament::icon icon="heroicon-o-plus" class="w-4 h-4" />
            </button>
            <button @click="open = false" type="button" class="p-1.5 rounded-lg hover:bg-white/20 transition">
                <x-filament::icon icon="heroicon-o-x-mark" class="w-4 h-4" />
            </button>
        </div>
    </div>

    {{-- Conversation Sidebar --}}
    @livewire('filament-copilot-sidebar', ['activeConversationId' => $conversationId])

    {{-- Messages --}}
    <div id="copilot-messages" x-ref="messages" class="flex-1 overflow-y-auto px-4 py-3 space-y-4">
        @if (empty($messages))
            <div
                class="flex flex-col items-center justify-center h-full text-center text-gray-400 dark:text-gray-500 gap-3">
                <x-filament::icon icon="heroicon-o-sparkles" class="w-10 h-10" />
                <p class="text-sm">{{ __('filament-copilot::filament-copilot.welcome_message') }}</p>

                {{-- Quick Actions --}}
                @if (!empty(($quickActions = config('filament-copilot.quick_actions', []))))
                    <div class="flex flex-wrap justify-center gap-2 mt-2">
                        @foreach ($quickActions as $action)
                            <button
                                wire:click="$dispatch('copilot-quick-action', { prompt: '{{ addslashes($action['prompt'] ?? $action) }}' })"
                                type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                @if (isset($action['icon']))
                                    <x-filament::icon :icon="$action['icon']" class="w-3.5 h-3.5" />
                                @endif
                                {{ $action['label'] ?? $action }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            @foreach ($messages as $msg)
                @include('filament-copilot::components.chat-message', ['msg' => $msg])
            @endforeach
        @endif

        {{-- SSE Streaming indicator --}}
        <template x-if="isStreaming && streamedContent">
            <div class="flex items-start gap-3">
                <div
                    class="w-7 h-7 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                    <x-filament::icon icon="heroicon-o-sparkles"
                        class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                </div>
                <div class="flex-1 prose prose-sm dark:prose-invert max-w-none">
                    <p x-text="streamedContent" class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                    </p>
                    <span class="inline-block w-2 h-4 bg-primary-500 animate-pulse ml-0.5"></span>
                </div>
            </div>
        </template>

        {{-- Loading indicator (synchronous fallback) --}}
        <div wire:loading wire:target="sendMessage" x-show="!isStreaming" class="flex items-start gap-3">
            <div
                class="w-7 h-7 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                <x-filament::icon icon="heroicon-o-sparkles" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
            </div>
            <div class="flex items-center gap-1 py-2">
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
            </div>
        </div>

        {{-- Streaming bouncing dots (before first chunk arrives) --}}
        <template x-if="isStreaming && !streamedContent">
            <div class="flex items-start gap-3">
                <div
                    class="w-7 h-7 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                    <x-filament::icon icon="heroicon-o-sparkles"
                        class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                </div>
                <div class="flex items-center gap-1 py-2">
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            </div>
        </template>
    </div>

    {{-- AskUser Question UI --}}
    @if ($pendingQuestion)
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/10">
            <div class="flex items-start gap-2 mb-2">
                <x-filament::icon icon="heroicon-o-question-mark-circle"
                    class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                <div>
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        {{ __('filament-copilot::filament-copilot.question_from_copilot') }}
                    </p>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                        {{ $pendingQuestion['question'] ?? '' }}
                    </p>
                    @if (!empty($pendingQuestion['context']))
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 italic">
                            {{ $pendingQuestion['context'] }}
                        </p>
                    @endif
                    @if (!empty($pendingQuestion['options']))
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach ($pendingQuestion['options'] as $option)
                                <button wire:click="respondToQuestion('{{ addslashes($option) }}')" type="button"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-full border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-800 transition">
                                    {{ $option }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Plan Approval --}}
    @if ($pendingPlan)
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-amber-50 dark:bg-amber-900/10">
            <div class="flex items-start gap-2 mb-2">
                <x-filament::icon icon="heroicon-o-clipboard-document-list"
                    class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                        {{ __('filament-copilot::filament-copilot.plan_proposed') }}
                    </p>
                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                        {{ $pendingPlan['description'] ?? '' }}
                    </p>
                    @if (!empty($pendingPlan['steps']))
                        <ol class="mt-2 space-y-1">
                            @foreach ($pendingPlan['steps'] as $i => $step)
                                <li class="text-xs text-amber-700 dark:text-amber-300">
                                    {{ $i + 1 }}. {{ $step['description'] ?? $step }}
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 justify-end">
                <x-filament::button wire:click="rejectPlan('{{ $pendingPlan['id'] }}')" color="danger"
                    size="xs">
                    {{ __('filament-copilot::filament-copilot.reject') }}
                </x-filament::button>
                <x-filament::button wire:click="approvePlan('{{ $pendingPlan['id'] }}')" color="success"
                    size="xs">
                    {{ __('filament-copilot::filament-copilot.approve') }}
                </x-filament::button>
            </div>
        </div>
    @endif

    {{-- Input --}}
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 shrink-0">
        <form wire:submit="sendMessage" class="flex gap-2">
            <textarea wire:model="message" @keydown="handleKeydown($event)" rows="1"
                class="flex-1 resize-none rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent placeholder-gray-400 dark:placeholder-gray-500"
                placeholder="{{ __('filament-copilot::filament-copilot.input_placeholder') }}"
                :disabled="$wire.isLoading || isStreaming"></textarea>
            <button type="submit"
                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-600 hover:bg-primary-700 text-white transition disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
                :disabled="$wire.isLoading || isStreaming">
                <x-filament::icon icon="heroicon-o-paper-airplane" class="w-4 h-4" />
            </button>
        </form>
    </div>
</div>
