<?php

use PowerComponents\LivewirePowerGrid\Tests\{DishesCollectionTable, DishesTable};

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

it('properly filters by "name is" using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('Name 1', 'is'))
    ->assertSee('Name 1')
    ->assertDontSee('Name 2')
    ->call('clearFilter', 'name')
    ->assertSeeText('Name 1');

it('properly filters by "name is" using nonexistent record using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('Name 6', 'is'))
    ->assertSee('No records found')
    ->assertDontSee('Name 1');

it('properly filters by "name is not" using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('Name 2', 'is_not'))
    ->assertSee('Name 1')
    ->assertDontSee('NAme 2')
    ->call('clearFilter', 'name')
    ->assertSee('Name 2');

it('properly filters by "name is not" using nonexistent record using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('Name 6', 'is_not'))
    ->assertSee('Name 5')
    ->assertSee('Name 4')
    ->call('clearFilter', 'name')
    ->assertViewHas('filters', []);

it('properly filters by "name contains" using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('4', 'contains'))
    ->assertSee('Name 4')
    ->assertDontSee('Name 2');

it('properly filters by "name contains" using nonexistent record using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('Name 6', 'contains'))
    ->assertSee('No records found')
    ->assertDontSee('Name 1')
    ->assertDontSee('Name 2');

it('properly filters by "name contains not" using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('5', 'contains_not'))
    ->assertDontSee('Name 5')
    ->assertSee('Name 1');

it('properly filters by "name contains not" using nonexistent record using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('Name 6', 'contains_not'))
    ->assertSee('Name 1')
    ->assertSee('Name 2');

it('properly filters by "name starts with" using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('Na', 'starts_with'))
    ->assertSee('Name 1')
    ->assertSee('Name 2');

it('properly filters by "name starts with" using nonexistent record using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('Nonexistent', 'starts_with'))
    ->assertSee('No records found')
    ->assertDontSee('Name 1')
    ->assertDontSee('Name 2');

it('properly filters by "name ends with" using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('e 5', 'ends_with'))
    ->assertSee('Name 5')
    ->assertDontSee('Name 1');

it('properly filters by "name ends with" using nonexistent record using collection table')
    ->livewire(DishesCollectionTable::class)
    ->set('filters', filterInputText('Nonexistent', 'ends_with'))
    ->assertSee('No records found')
    ->assertDontSee('Name 1')
    ->assertDontSee('Name 2')
    ->assertDontSee('Name 3');

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
