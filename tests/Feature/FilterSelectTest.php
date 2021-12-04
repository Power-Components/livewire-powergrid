<?php

use function Pest\Livewire\livewire;

it('property displays the results and options', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtmlInOrder([
            'wire:input.debounce.500ms="filterSelect(\'category_id\',\'Categoria\')"',
            'wire:model.debounce.500ms="filters.select.category_id"',
        ]);
})->with('themes');

it('properly filter with category_id', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterSelect('category_id', 1))
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertDontSee('Pastel de Nata');
})->with('themes');

it('properly filter with another category_id', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterSelect('category_id', 3))
        ->assertSee('Empadão de Palmito')
        ->assertSee('Empadão de Alcachofra')
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê');
})->with('themes');

function filterSelect(string $dataField, ?string $value): array
{
    return [
        'select' => [
            $dataField => $value,
        ],
    ];
}
