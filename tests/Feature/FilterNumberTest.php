<?php

use PowerComponents\LivewirePowerGrid\Tests\{DishesCollectionTable, DishesTable};

it('properly filters by "min"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', '2', null, '', ''))
    ->assertSee('Peixada da chef Nábia')
    ->assertSee('Francesinha')
    ->assertSee('борщ')
    ->assertDontSee('Pastel de Nata');

it('properly filters by "min" - using collection')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterNumber('price', 1.68, null, '', ''))
    ->assertSee('Name 2')
    ->assertSee('Name 3')
    ->assertSee('Name 4')
    ->assertDontSee('Name 1');

it('properly filters by "max"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', null, '3', '', ''))
    ->assertSee('Pastel de Nata')
    ->assertSee('Peixada da chef Nábia')
    ->assertSee('Carne Louca')
    ->assertDontSee('Bife à Rolê');

it('properly filters by "max" - using collection')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterNumber('price', null, 1.68, '', ''))
    ->assertSee('Name 1')
    ->assertSee('Name 2')
    ->assertDontSee('Name 3');

it('properly filters by "min & max"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', '1', '2', '', ''))
    ->assertSee('Pastel de Nata')
    ->assertSee('Peixada da chef Nábia')
    ->assertDontSee('Carne Louca');

it('properly filters by "min & max" - using collection')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterNumber('price', 1.68, 1.78, '', ''))
    ->assertSee('Name 2')
    ->assertSee('Name 3')
    ->assertDontSee('Name 1')
    ->assertDontSee('Name 4')
    ->assertDontSee('Name 5');

it('properly filters by "min & max" currency')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('price', '60.49', '100', '', ''))
    ->assertSee('Francesinha')
    ->assertSee('Barco-Sushi da Sueli')
    ->assertSee('Barco-Sushi Simples')
    ->assertSee('Polpetone Filé Mignon')
    ->assertDontSee('борщ');

it('ignores null "min & max"')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', null, null, '', ''))
    ->assertSee('Pastel de Nata')
    ->assertSee('Peixada da chef Nábia')
    ->assertSee('борщ');

it('ignores null "min & max" - using collection')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterNumber('price', null, null, '', ''))
    ->assertSee('Name 1')
    ->assertSee('Name 2')
    ->assertSee('Name 3')
    ->assertSee('Name 4')
    ->assertSee('Name 5');

it('displays "No records found" with non-existent min')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('id', '1000000', null, '', ''))
    ->assertSee('No records found')
    ->assertDontSee('Pastel de Nata');

it('displays "No records found" with non-existent min - using collection')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('price', '1000000', null, '', ''))
    ->assertSee('No records found')
    ->assertDontSee('Name 1');

it('properly filters by "min & max" formatted')
    ->livewire(DishesTable::class)
    ->set('filters', filterNumber('price', '1,50', '20,51', '.', ','))
    ->assertSee('Pastel de Nata')
    ->assertSee('Peixada da chef Nábia')
    ->assertDontSee('Carne Louca');

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
