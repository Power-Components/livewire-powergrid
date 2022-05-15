<?php

use function Pest\Livewire\livewire;

it('property displays the results and options', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtmlInOrder([
            'wire:input.debounce.500ms="filterSelect(\'dishes.diet\',\'Dieta\')"',
            'wire:model.debounce.500ms="filters.select.dishes.diet"',
        ])
        ->assertSeeHtmlInOrder([
            'All',
            '<option value="0">',
            '🍽️ All diets',
            '<option value="1">',
            '🌱 Suitable for Vegans',
            '<option value="2">',
            '🥜 Suitable for Celiacs',
        ]);
})->with('enum')->onlyFromPhp('8.1');

it('properly filter with diet', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterEnumSelect('diet', 1))
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertDontSee('Pastel de Nata');
})->with('enum')->onlyFromPhp('8.1');

function filterEnumSelect(string $dataField, ?string $value): array
{
    return [
        'select' => [
            $dataField => $value,
        ],
    ];
}
