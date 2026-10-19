<?php

use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Support\IconRenderer;

it('renders a known icon component', function () {
    expect(IconRenderer::render('livewire-powergrid::icons.search'))->toContain('<svg');
});

it('returns an empty string for an unknown icon', function () {
    expect(IconRenderer::render('zz-unknown-icon'))->toBe('');
});

it('does not break an outer blade component when the icon is unknown', function () {
    view()->addNamespace('tests', __DIR__.'/../../resources/views/tests');

    $html = Blade::render(<<<'BLADE'
        @component('tests::footer-top')
            {{ \PowerComponents\LivewirePowerGrid\Support\IconRenderer::render('zz-missing-icon-in-component') }}
        @endcomponent
        BLADE);

    expect($html)->toBeString();
});
