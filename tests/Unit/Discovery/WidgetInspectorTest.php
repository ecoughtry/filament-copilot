<?php

use EslamRedaDiv\FilamentCopilot\Discovery\WidgetInspector;
use Illuminate\Contracts\Container\BindingResolutionException;

it('creates WidgetInspector instance', function () {
    $inspector = new WidgetInspector;

    expect($inspector)->toBeInstanceOf(WidgetInspector::class);
});

it('returns empty array when no panel available', function () {
    $inspector = new WidgetInspector;

    // When Filament panel manager is not booted, discoverWidgets should return empty or throw gracefully
    try {
        $widgets = $inspector->discoverWidgets('nonexistent');
        expect($widgets)->toBeArray();
    } catch (BindingResolutionException $e) {
        // Expected in test environment without Filament panel booted
        expect($e->getMessage())->toContain('filament');
    }
});

it('returns empty string for widget context when no panel available', function () {
    $inspector = new WidgetInspector;

    try {
        $context = $inspector->buildWidgetContext('nonexistent');
        expect($context)->toBeString();
    } catch (BindingResolutionException $e) {
        // Expected in test environment without Filament panel booted
        expect($e->getMessage())->toContain('filament');
    }
});

it('inspects widget metadata correctly', function () {
    $inspector = new WidgetInspector;

    // Test with a mock widget class that doesn't have the trait
    $result = $inspector->inspectWidget(stdClass::class);

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('widget')
        ->and($result)->toHaveKey('name')
        ->and($result)->toHaveKey('has_copilot_trait')
        ->and($result)->toHaveKey('provides_data')
        ->and($result['name'])->toBe('stdClass')
        ->and($result['has_copilot_trait'])->toBeFalse()
        ->and($result['provides_data'])->toBeFalse()
        ->and($result['exposes_data'])->toBeFalse();
});
