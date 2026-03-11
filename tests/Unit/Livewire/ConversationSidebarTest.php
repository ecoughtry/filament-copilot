<?php

use EslamRedaDiv\FilamentCopilot\Livewire\ConversationSidebar;

it('ConversationSidebar class exists', function () {
    expect(class_exists(ConversationSidebar::class))->toBeTrue();
});

it('ConversationSidebar has required methods', function () {
    $methods = ['toggle', 'open', 'close', 'selectConversation', 'deleteConversation', 'newConversation'];

    foreach ($methods as $method) {
        expect(method_exists(ConversationSidebar::class, $method))->toBeTrue();
    }
});

it('ConversationSidebar has render method', function () {
    expect(method_exists(ConversationSidebar::class, 'render'))->toBeTrue();
});
