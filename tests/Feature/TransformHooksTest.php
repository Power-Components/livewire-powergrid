<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTransformHooksTable;
use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, DaisyUI, Tailwind};

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$themes = [
    'tailwind' => [DishesTransformHooksTable::class, (object) ['theme' => Tailwind::class]],
    'bootstrap' => [DishesTransformHooksTable::class, (object) ['theme' => Bootstrap5::class]],
    'daisyui' => [DishesTransformHooksTable::class, (object) ['theme' => DaisyUI::class]],
];

it('transformRows modifies row data before rendering', function (string $component, object $params) {
    // Without transformRows: custom_label column should be empty
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertDontSee('custom-1')
        ->assertDontSee('custom-2');

    // With transformRows: rows are enriched with custom_label
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('useTransformRows', true)
        ->assertSee('custom-1')
        ->assertSee('custom-2')
        ->assertSee('custom-3');
})->with($themes);

it('transformQuery filters the query before pagination', function (string $component, object $params) {
    // Without transformQuery: out-of-stock dishes are visible
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSee('Francesinha')
        ->assertSee('Barco-Sushi da Sueli');

    // With transformQuery: only in_stock dishes remain
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('useTransformQuery', true)
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertDontSee('Polpetone Filé Mignon');
})->with($themes);

it('transformActions modifies action attributes before rendering', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('useTransformActions', true)
        ->assertHasAction('edit');
})->with($themes);

it('transformRows and transformQuery can be combined', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('useTransformRows', true)
        ->set('useTransformQuery', true)
        ->assertSee('custom-1')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertDontSee('Polpetone Filé Mignon');
})->with($themes);
