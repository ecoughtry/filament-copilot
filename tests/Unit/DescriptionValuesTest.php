<?php

use EslamRedaDiv\FilamentCopilot\Descriptions\BooleanValue;
use EslamRedaDiv\FilamentCopilot\Descriptions\DateValue;
use EslamRedaDiv\FilamentCopilot\Descriptions\ListValue;
use EslamRedaDiv\FilamentCopilot\Descriptions\NumericValue;
use EslamRedaDiv\FilamentCopilot\Descriptions\RelationValue;
use EslamRedaDiv\FilamentCopilot\Descriptions\TextValue;

it('creates a text value with description', function () {
    $value = TextValue::make('name')
        ->description('The user name');

    $array = $value->toArray();

    expect($array['label'])->toBe('name')
        ->and($array['type'])->toBe('text')
        ->and($array['description'])->toBe('The user name');
});

it('text value is stringable', function () {
    $value = TextValue::make('email')->description('User email');

    expect((string) $value)->toContain('email');
});

it('creates a numeric value', function () {
    $value = NumericValue::make('age')
        ->description('User age')
        ->min(1)
        ->max(150);

    $array = $value->toArray();

    expect($array['type'])->toBe('numeric')
        ->and($array['min'])->toBe(1.0)
        ->and($array['max'])->toBe(150.0);
});

it('creates a boolean value', function () {
    $value = BooleanValue::make('is_active')
        ->description('Whether the user is active');

    $array = $value->toArray();

    expect($array['type'])->toBe('boolean');
});

it('creates a date value', function () {
    $value = DateValue::make('created_at')
        ->description('Creation date')
        ->format('Y-m-d');

    $array = $value->toArray();

    expect($array['type'])->toBe('date')
        ->and($array['format'])->toBe('Y-m-d');
});

it('creates a relation value', function () {
    $value = RelationValue::make('posts')
        ->description('User posts')
        ->relatedModel('Post');

    $array = $value->toArray();

    expect($array['type'])->toBe('relation')
        ->and($array['related_model'])->toBe('Post');
});

it('creates a list value', function () {
    $value = ListValue::make('status')
        ->description('Status options')
        ->options(['active' => 'Active', 'inactive' => 'Inactive']);

    $array = $value->toArray();

    expect($array['type'])->toBe('list')
        ->and($array['options'])->toBe(['active' => 'Active', 'inactive' => 'Inactive']);
});
