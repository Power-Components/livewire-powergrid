<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('properly filters by "min"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', '2', null, '', ''))
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertSeeHtml('Francesinha')
    ->assertSeeHtml('борщ')
    ->assertDontSeeHtml('Pastel de Nata');

it('properly filters by "max"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', null, '3', '', ''))
    ->assertSeeHtml('Pastel de Nata')
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertSeeHtml('Carne Louca')
    ->assertDontSeeHtml('Bife à Rolê');

it('properly filters by "min & max"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', '1', '2', '', ''))
    ->assertSeeHtml('Pastel de Nata')
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertDontSeeHtml('Carne Louca');

it('properly filters by "min & max" currency')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('price', '60.49', '100', '', ''))
    ->assertSeeHtml('Francesinha')
    ->assertSeeHtml('Barco-Sushi da Sueli')
    ->assertSeeHtml('Barco-Sushi Simples')
    ->assertSeeHtml('Polpetone Filé Mignon')
    ->assertDontSeeHtml('борщ');

it('ignores null "min & max"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', null, null, '', ''))
    ->assertSeeHtml('Pastel de Nata')
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertSeeHtml('борщ');

it('displays "No records found" with non-existent min')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', '1000000', null, '', ''))
    ->assertSeeHtml('No records found')
    ->assertDontSeeHtml('Pastel de Nata');

it('properly filters by "min & max" formatted')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('price', '1,50', '20,51', '.', ','))
    ->assertSeeHtml('Pastel de Nata')
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertDontSeeHtml('Carne Louca');

function filterNumber(string $field, ?string $min, ?string $max, ?string $thousands, ?string $decimal): array
{
    return [
        'number' => [
            $field => [
                'start'     => $min,
                'end'       => $max,
                'thousands' => $thousands,
                'decimal'   => $decimal,
            ],
        ],
    ];
}
