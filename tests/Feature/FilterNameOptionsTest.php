<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('properly filters by "name is"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Francesinha', 'is'))
    ->assertSee('Francesinha')
    ->assertDontSee('Francesinha vegana')
    ->call('clearFilter', 'name')
    ->assertSee('Francesinha vegana');

it('properly filters by "name is" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent dish', 'is'))
    ->assertSee('No records found')
    ->assertDontSee('Francesinha');

it('properly filters by "name is not"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Francesinha vegana', 'is_not'))
    ->assertSee('Francesinha')
    ->assertDontSee('Francesinha vegana')
    ->call('clearFilter', 'name')
    ->assertSee('Francesinha vegana');

it('properly filters by "name is not" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent dish', 'is_not'))
    ->assertSee('Francesinha')
    ->assertSee('Francesinha vegana')
    ->call('clearFilter', 'name')
    ->assertViewHas('filters', []);

it('properly filters by "name contains"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('francesinha', 'contains'))
    ->assertSee('Francesinha')
    ->assertSee('Francesinha vegana');

it('properly filters by "name contains" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent dish', 'contains'))
    ->assertSee('No records found')
    ->assertDontSee('Francesinha')
    ->assertDontSee('Francesinha vegana');

it('properly filters by "name contains not"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('francesinha', 'contains_not'))
    ->assertDontSee('Francesinha')
    ->assertDontSee('Francesinha vegana');

it('properly filters by "name contains not" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent dish', 'contains_not'))
    ->assertSee('Francesinha')
    ->assertSee('Francesinha vegana');

it('properly filters by "name starts with"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('fran', 'starts_with'))
    ->assertSee('Francesinha')
    ->assertSee('Francesinha vegana')
    ->assertDontSee('Barco-Sushi da Sueli');

it('properly filters by "name starts with" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent', 'starts_with'))
    ->assertSee('No records found')
    ->assertDontSee('Francesinha')
    ->assertDontSee('Francesinha vegana')
    ->assertDontSee('Barco-Sushi da Sueli');

it('properly filters by "name ends with"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('vegana', 'ends_with'))
    ->assertSee('Francesinha vegana')
    ->assertDontSee('Barco-Sushi da Sueli');

it('properly filters by "name ends with" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent', 'ends_with'))
    ->assertSee('No records found')
    ->assertDontSee('Francesinha')
    ->assertDontSee('Francesinha vegana')
    ->assertDontSee('Barco-Sushi da Sueli');

function filterInputText(string $text, string $type): array
{
    return [
        'input_text' => [
            'name' => $text,
        ],
        'input_text_options' => [
            'name' => $type,
        ],
    ];
}
