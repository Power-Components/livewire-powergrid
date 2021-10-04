<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('properly filters by bool true')

    ->livewire(DishesTable::class)
    ->set('filters', filterBoolean('in_stock', 'true'))
    ->assertSeeHtml('Pastel de Nata')
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertSeeHtml('Carne Louca')
    ->assertSeeHtml('Bife à Rolê')
    ->assertSeeHtml('Francesinha vegana')
    ->assertDontSeeHtml('Francesinha </div>')
    ->assertDontSeeHtml('Barco-Sushi da Sueli')
    ->assertDontSeeHtml('Barco-Sushi Simples')
    ->assertDontSeeHtml('Polpetone Filé Mignon')
    ->assertDontSeeHtml('борщ');

it('properly filters by bool false')
    ->livewire(DishesTable::class)
    ->set('filters', filterBoolean('in_stock', 'false'))
    ->assertSeeHtml('Francesinha')
    ->assertSeeHtml('Barco-Sushi da Sueli')
    ->assertSeeHtml('Barco-Sushi Simples')
    ->assertSeeHtml('Polpetone Filé Mignon')
    ->assertSeeHtml('борщ')
    ->assertDontSeeHtml('Pastel de Nata')
    ->assertDontSeeHtml('Peixada da chef Nábia')
    ->assertDontSeeHtml('Carne Louca')
    ->assertDontSeeHtml('Bife à Rolê')
    ->assertDontSeeHtml('Francesinha vegana');

it('properly filters by bool "all"')
    ->livewire(DishesTable::class)
    ->set('filters', filterBoolean('in_stock', 'all'))
    ->assertSeeHtml('Pastel de Nata')
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertSeeHtml('Carne Louca')
    ->assertSeeHtml('Bife à Rolê')
    ->assertSeeHtml('Francesinha vegana')
    ->assertSeeHtml('Francesinha')
    ->assertSeeHtml('Barco-Sushi da Sueli')
    ->assertSeeHtml('Barco-Sushi Simples')
    ->assertSeeHtml('Polpetone Filé Mignon')
    ->assertSeeHtml('борщ');
    
//Helper

function filterBoolean(string $field, ?string $value): array
{
    return [
        'boolean' => [
            $field => $value,
        ]
    ];
}
