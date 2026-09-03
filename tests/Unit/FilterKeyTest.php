<?php

use PowerComponents\LivewirePowerGrid\Support\FilterKey;

uses()->group('filters');

it('encodes and decodes a qualified column dot-free', function () {
    $encoded = FilterKey::encode('dishes.name');

    expect($encoded)->not->toContain('.')
        ->and(FilterKey::decode($encoded))->toBe('dishes.name');
});

it('leaves an unqualified column untouched through a round-trip', function () {
    expect(FilterKey::encode('price'))->toBe('price')
        ->and(FilterKey::decode('price'))->toBe('price');
});

it('encodes the field-level keys of a full draft structure', function () {
    $encoded = FilterKey::encodeDraft([
        'input_text' => ['dishes.name' => 'a'],
        'number' => ['price' => ['start' => '1', 'end' => '9']],
    ]);

    expect($encoded['input_text'])->toHaveKey('dishes__pgdot__name')
        ->and($encoded['input_text'])->not->toHaveKey('dishes.name')
        ->and($encoded['number'])->toHaveKey('price');
});

it('decodes the field-level keys back to the real columns', function () {
    $draft = [
        'input_text' => ['dishes__pgdot__name' => 'a'],
        'multi_select' => ['category_id' => ['values' => [1]]],
    ];

    $decoded = FilterKey::decodeDraft($draft);

    expect($decoded['input_text'])->toHaveKey('dishes.name')
        ->and($decoded['input_text']['dishes.name'])->toBe('a')
        ->and($decoded['multi_select'])->toHaveKey('category_id');
});

it('builds a deferred wire:model plus a stable data-pg-draft path', function () {
    expect(FilterKey::draftModel('input_text.name'))->toBe([
        'wire:model' => 'draftFilters.input_text.name',
        'data-pg-draft' => 'input_text.name',
    ]);
});

it('is a stable round-trip for a full draft', function () {
    $draft = [
        'input_text' => ['dishes.name' => 'x'],
        'number' => ['price' => ['start' => '1']],
    ];

    expect(FilterKey::decodeDraft(FilterKey::encodeDraft($draft)))->toBe($draft);
});
