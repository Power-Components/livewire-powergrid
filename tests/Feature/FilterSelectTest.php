<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('property displays the results and options', function () {
    livewire(DishesTable::class)
        ->assertSeeHtmlInOrder([
            'wire:input.debounce.500ms="filterSelect(\'category_id\',\'Categoria\')"',
            'wire:model.debounce.500ms="filters.select.category_id"',
        ]);
});

it('properly filter with category_id', function () {
    livewire(DishesTable::class)
        ->set('filters', filterSelect('category_id', 1))
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertDontSee('Pastel de Nata');
});

it('properly filter with another category_id', function () {
    livewire(DishesTable::class)
        ->set('filters', filterSelect('category_id', 3))
        ->assertSee('Empadão de Palmito')
        ->assertSee('Empadão de Alcachofra')
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê');
});

function filterSelect(string $dataField, ?string $value): array
{
    return [
        'select' => [
            $dataField => $value,
        ],
    ];
}
