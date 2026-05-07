<?php

use PowerComponents\LivewirePowerGrid\Themes\{Base, Theme};
use PowerComponents\LivewirePowerGrid\Themes\Components\ThemeBuilder;

it('validates the base theme structure and fallback views', function () {
    $theme = new Base();

    // Validar tokens do header
    expect($theme->get('header.layout.container'))->toBe('d-flex flex-column flex-md-row justify-content-between align-items-center mb-3')
        ->and($theme->get('header.layout.sub_container'))->toBe('d-flex flex-row gap-2');

    // Validar tokens da table
    expect($theme->get('table.layout.container'))->toBe('table-responsive')
        ->and($theme->get('table.layout.table'))->toBe('table table-bordered table-striped')
        ->and($theme->get('table.layout.thead'))->toBe('table-light');

    // Validar tokens do footer
    expect($theme->get('footer.layout.container'))->toBe('d-flex justify-content-between align-items-center mt-3')
        ->and($theme->get('footer.layout.select'))->toBe('form-select w-auto');
});

it('resolves fallback views to the base framework when not defined', function () {
    $theme = new Base();

    // O Base theme não define views nem baseView, deve cair no fallback 'structure'
    expect($theme->resolveView('header'))
        ->toBe('livewire-powergrid::components.structure.header')
        ->and($theme->resolveView('footer'))
        ->toBe('livewire-powergrid::components.structure.footer')
        ->and($theme->resolveView('table'))
        ->toBe('livewire-powergrid::components.structure.table');
});

it('supports array structure in ThemeBuilder for a custom theme', function () {
    $theme = new class() extends Theme
    {
        public function struct(): ThemeBuilder
        {
            return ThemeBuilder::make('custom')
                ->header([
                    'layout' => ['container' => 'custom-header-class'],
                ])
                ->table([
                    'layout' => ['table' => 'custom-table-class'],
                ]);
        }
    };

    expect($theme->get('header.layout.container'))->toBe('custom-header-class')
        ->and($theme->get('table.layout.table'))->toBe('custom-table-class');
});
