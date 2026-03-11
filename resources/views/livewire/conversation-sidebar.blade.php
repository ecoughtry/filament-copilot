<div x-data="{
    sidebarOpen: @entangle('isOpen'),
}" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 -translate-x-4" x-cloak
    class="absolute inset-0 z-10 bg-white dark:bg-gray-900 flex flex-col">
    {{-- Sidebar Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-semibold text-sm text-gray-900 dark:text-white">
            {{ __('filament-copilot::filament-copilot.conversation_history') }}
        </h3>
        <div class="flex items-center gap-1">
            <button wire:click="newConversation" type="button"
                class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                title="{{ __('filament-copilot::filament-copilot.new_conversation') }}">
                <x-filament::icon icon="heroicon-o-plus" class="w-4 h-4 text-gray-500" />
            </button>
            <button wire:click="close" type="button"
                class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <x-filament::icon icon="heroicon-o-x-mark" class="w-4 h-4 text-gray-500" />
            </button>
        </div>
    </div>

    {{-- Conversation List --}}
    <div class="flex-1 overflow-y-auto p-2 space-y-1">
        @forelse($conversations as $conv)
            <div
                class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 group cursor-pointer transition {{ $conv['id'] === $activeConversationId ? 'bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800' : '' }}">
                <button wire:click="selectConversation('{{ $conv['id'] }}')" type="button"
                    class="flex-1 text-left truncate">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ $conv['title'] ?: __('filament-copilot::filament-copilot.untitled') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $conv['updated_at'] }}
                        @if (isset($conv['message_count']))
                            &middot; {{ $conv['message_count'] }}
                            {{ __('filament-copilot::filament-copilot.messages') }}
                        @endif
                    </p>
                </button>
                <button wire:click="deleteConversation('{{ $conv['id'] }}')"
                    wire:confirm="{{ __('filament-copilot::filament-copilot.confirm_delete') }}" type="button"
                    class="opacity-0 group-hover:opacity-100 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                    <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4 text-red-500" />
                </button>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ __('filament-copilot::filament-copilot.no_conversations') }}
            </div>
        @endforelse
    </div>
</div>
