<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesEnumTable;
use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, DaisyUI, Tailwind};

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it(
    'property displays the results and options',
    fn (string $component, object $params) => livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSeeHtmlInOrder([
            'wire:model="filters.select.dishes.diet"',
            'wire:input.live.debounce.600ms="filterSelect(\'dishes.diet\', \'Dieta\')"',
        ])
        ->assertSeeHtmlInOrder([
            'All',
            '<option', 'wire:key="select-testing-dishes-enum-table-0"',
            'value="0"',
            '🍽️ All diets',
            '<option', 'wire:key="select-testing-dishes-enum-table-1"',
            'value="1"',
            '🌱 Suitable for Vegans',
            '<option', 'wire:key="select-testing-dishes-enum-table-2"',
            'value="2"',
            '🥜 Suitable for Celiacs',
        ])
)->group('filters')->with('enum_themes');

it(
    'properly filter with diet',
    fn (string $component, object $params) => livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('filters', filterEnumSelect('diet', 1))
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertDontSee('Pastel de Nata')
)->group('filters')->with('enum_themes');

function filterEnumSelect(string $dataField, ?string $value): array
{
    return [
        'select' => [
            $dataField => $value,
        ],
    ];
}

dataset('enum_themes', [
    'tailwind -> id' => [DishesEnumTable::class, (object) ['theme' => Tailwind::class, 'field' => 'id']],
    'bootstrap -> id' => [DishesEnumTable::class, (object) ['theme' => Bootstrap5::class, 'field' => 'id']],
    'daisyui -> id' => [DishesEnumTable::class, (object) ['theme' => DaisyUI::class, 'field' => 'id']],
]);
