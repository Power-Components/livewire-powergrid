<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('properly filters by "min"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', min: 2, max: null))
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertSeeHtml('Francesinha')
    ->assertSeeHtml('борщ')
    ->assertDontSeeHtml('Pastel de Nata');
   
it('properly filters by "max"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', min: null, max: 3))
    ->assertSeeHtml('Pastel de Nata')
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertSeeHtml('Carne Louca')
    ->assertDontSeeHtml('Bife à Rolê');

it('properly filters by "min & max"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', min: 1, max: 2))
    ->assertSeeHtml('Pastel de Nata')
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertDontSeeHtml('Carne Louca');

it('properly filters by "min & max" currency')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('price', min: 60.49, max: 100))
    ->assertSeeHtml('Francesinha')
    ->assertSeeHtml('Barco-Sushi da Sueli')
    ->assertSeeHtml('Barco-Sushi Simples')
    ->assertSeeHtml('Polpetone Filé Mignon')
    ->assertDontSeeHtml('борщ');

it('ignores null "min & max"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', min: null, max: null))
    ->assertSeeHtml('Pastel de Nata')
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertSeeHtml('борщ');

it('displays "No records found" with non-existent min')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', min: 1000000, max: null))
    ->assertSeeHtml('No records found')
    ->assertDontSeeHtml('Pastel de Nata');


//Helper
function filterNumber(string $field, ?int $min, ?int $max): array
{
    return [
        'number' => [
            $field =>  [
                'start' => $min,
                'end' => $max,
                'thousands' => '',
                'decimal' => ''
            ]
        ]
    ];
}
