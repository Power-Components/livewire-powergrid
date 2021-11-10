<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('properly filters by bool true')
    ->livewire(DishesTable::class)
    ->assertSee('Em Estoque')
    ->assertSeeHtml('wire:input.debounce.300ms="filterBoolean(\'in_stock\', $event.target.value, \'Em Estoque\')"')
    ->set('filters', filterBoolean('in_stock', 'true'))
    ->assertSee('Pastel de Nata')
    ->assertSee('Peixada da chef Nábia')
    ->assertSee('Carne Louca')
    ->assertSee('Bife à Rolê')
    ->assertSee('Francesinha vegana')
    ->assertDontSeeHtml('Francesinha </div>')
    ->assertDontSee('Barco-Sushi da Sueli')
    ->assertDontSee('Barco-Sushi Simples')
    ->assertDontSee('Polpetone Filé Mignon')
    ->assertDontSee('борщ')
    ->call('clearFilter', 'in_stock')
    ->assertSee('Francesinha')
    ->assertSee('Barco-Sushi da Sueli')
    ->assertSee('Barco-Sushi Simples')
    ->assertSee('Polpetone Filé Mignon')
    ->assertSee('борщ');

it('properly filters by bool false')
    ->livewire(DishesTable::class)
    ->set('filters', filterBoolean('in_stock', 'false'))
    ->assertSee('Francesinha')
    ->assertSee('Barco-Sushi da Sueli')
    ->assertSee('Barco-Sushi Simples')
    ->assertSee('Polpetone Filé Mignon')
    ->assertSee('борщ')
    ->assertDontSee('Pastel de Nata')
    ->assertDontSee('Peixada da chef Nábia')
    ->assertDontSee('Carne Louca')
    ->assertDontSee('Bife à Rolê')
    ->assertDontSee('Francesinha vegana')
    ->call('clearFilter', 'in_stock')
    ->assertSee('Pastel de Nata')
    ->assertSee('Peixada da chef Nábia')
    ->assertSee('Carne Louca')
    ->assertSee('Bife à Rolê')
    ->assertSee('Francesinha vegana');

it('properly filters by bool "all"')
    ->livewire(DishesTable::class)
    ->set('filters', filterBoolean('in_stock', 'all'))
    ->assertSee('Pastel de Nata')
    ->assertSee('Peixada da chef Nábia')
    ->assertSee('Carne Louca')
    ->assertSee('Bife à Rolê')
    ->assertSee('Francesinha vegana')
    ->assertSee('Francesinha')
    ->assertSee('Barco-Sushi da Sueli')
    ->assertSee('Barco-Sushi Simples')
    ->assertSee('Polpetone Filé Mignon')
    ->assertSee('борщ');

function filterBoolean(string $field, ?string $value): array
{
    return [
        'boolean' => [
            $field => $value,
        ],
    ];
}
