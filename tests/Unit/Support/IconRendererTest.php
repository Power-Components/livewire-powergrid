<?php

use PowerComponents\LivewirePowerGrid\Support\IconRenderer;

beforeEach(fn () => IconRenderer::flush());

it('returns an empty string for an empty icon', function () {
    expect(IconRenderer::render(''))->toBe('');
});

it('renders a package icon component with its attributes', function () {
    $html = IconRenderer::render('livewire-powergrid::icons.trash', ['class' => 'w-4 h-4']);

    expect($html)->toContain('<svg')
        ->and($html)->toContain('w-4 h-4');
});

it('statically folds namespaced icon components instead of x-dynamic-component', function () {
    $html = IconRenderer::render('livewire-powergrid::icons.filter', ['class' => 'w-5']);

    expect($html)->toContain('<svg')
        ->and($html)->not->toContain('x-dynamic-component');
});

it('returns an empty string when the component does not exist', function () {
    expect(IconRenderer::render('livewire-powergrid::icons.does-not-exist'))->toBe('');
});

it('opts icon components into blaze fold and memo', function () {
    $icon = file_get_contents(__DIR__.'/../../../resources/views/components/icons/chevron-up-down.blade.php');
    $filter = file_get_contents(__DIR__.'/../../../resources/views/components/icons/filter.blade.php');

    expect($icon)->toStartWith('@blaze(fold: true, memo: true)')
        ->and($filter)->toContain("@blaze(fold: true, memo: true, unsafe: ['attributes'])");
});

it('caches the compiled html per icon and attributes', function () {
    $first = IconRenderer::render('livewire-powergrid::icons.trash', ['class' => 'a']);
    $second = IconRenderer::render('livewire-powergrid::icons.trash', ['class' => 'a']);
    $third = IconRenderer::render('livewire-powergrid::icons.trash', ['class' => 'b']);

    expect($first)->toBe($second)
        ->and($third)->not->toBe($first);
});
