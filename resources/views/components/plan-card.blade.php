@props(['plan'])

<div class="rounded-lg border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/10 p-3">
    <div class="flex items-start gap-2 mb-2">
        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
        <div class="flex-1">
            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                {{ $plan['description'] ?? __('filament-copilot::filament-copilot.plan_proposed') }}
            </p>
        </div>
    </div>

    @if (!empty($plan['steps']))
        <ol class="ml-7 space-y-1 mb-3">
            @foreach ($plan['steps'] as $i => $step)
                <li class="text-xs text-amber-700 dark:text-amber-300 flex items-start gap-1.5">
                    <span class="font-mono font-semibold shrink-0">{{ $i + 1 }}.</span>
                    <span>{{ $step['description'] ?? $step }}</span>
                </li>
            @endforeach
        </ol>
    @endif

    {{ $slot }}
</div>
