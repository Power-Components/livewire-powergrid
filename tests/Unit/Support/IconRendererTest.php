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

it('returns an empty string when the component does not exist', function () {
    expect(IconRenderer::render('livewire-powergrid::icons.does-not-exist'))->toBe('');
});

it('caches the compiled html per icon and attributes', function () {
    $first = IconRenderer::render('livewire-powergrid::icons.trash', ['class' => 'a']);
    $second = IconRenderer::render('livewire-powergrid::icons.trash', ['class' => 'a']);
    $third = IconRenderer::render('livewire-powergrid::icons.trash', ['class' => 'b']);

    expect($first)->toBe($second)
        ->and($third)->not->toBe($first);
});
