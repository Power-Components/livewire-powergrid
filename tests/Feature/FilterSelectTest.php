<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('propertly  displays the results and options', function () {
    livewire(DishesTable::class)
        ->assertSeeHtmlInOrder([
            'wire:input.debounce.500ms="filterSelect(\'category_id\',\'Categoria\')"',
            'wire:model.debounce.500ms="filters.select.category_id"',
            '<option value="">All</option>',
            '<option value="">Carnes</option>',
            '<option value="">Peixe</option>',
        ]);
});

it('properly filter with category_id', function () {
    livewire(DishesTable::class)
        ->set('filters', filterSelect('category_id', 1))
        ->assertSeeHtml('Peixada da chef Nábia')
        ->assertSeeHtml('Carne Louca')
        ->assertSeeHtml('Bife à Rolê')
        ->assertDontSeeHtml('Pastel de Nata');
});

it('properly filter with another category_id', function () {
    livewire(DishesTable::class)
        ->set('filters', filterSelect('category_id', 3))
        ->assertSeeHtml('Empadão de Palmito')
        ->assertSeeHtml('Empadão de Alcachofra')
        ->assertDontSeeHtml('Peixada da chef Nábia')
        ->assertDontSeeHtml('Carne Louca')
        ->assertDontSeeHtml('Bife à Rolê');
});


function filterSelect(string $dataField, ?string $value): array
{
    return [
        'select' => [
            $dataField => $value,
        ],
    ];
}
