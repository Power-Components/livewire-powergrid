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

it('properly filters by "chef name is blank"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('', 'is_blank', 'chef_name'))
    ->assertSee('Carne Louca')
    ->assertDontSee('Pastel de Nata')
    ->assertDontSee('Francesinha vegana')
    ->set('perPage', '50')
    ->assertSee('Carne Louca')
    ->assertDontSee('Pastel de Nata')
    ->assertDontSee('Francesinha vegana');

it('properly filters by "chef name is NOT blank"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('', 'is_not_blank', 'chef_name'))
    ->assertSee('Pastel de Nata')
    ->assertSee('Francesinha vegana')
    ->assertDontSee('Carne Louca')
    ->set('perPage', '50')
    ->assertSee('Pastel de Nata')
    ->assertSee('Francesinha vegana')
    ->assertDontSee('Carne Louca');

it('properly filters by "chef name is null"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('', 'is_null', 'chef_name'))
    ->assertSee('Pastel de Nata')
    ->assertDontSee('Francesinha vegana')
    ->assertDontSee('Carne Louca')
    ->set('perPage', '50')
    ->assertSee('Pastel de Nata')
    ->assertDontSee('Francesinha vegana')
    ->assertDontSee('Carne Louca');

it('properly filters by "chef name is NOT null"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('', 'is_not_null', 'chef_name'))
    ->assertSee('Francesinha vegana')
    ->assertSee('Carne Louca')
    ->assertDontSee('Pastel de Nata')
    ->set('perPage', '50')
    ->assertSee('Francesinha vegana')
    ->assertSee('Carne Louca')
    ->assertDontSee('Pastel de Nata');

it('properly filters by "chef name is empty"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('', 'is_empty', 'chef_name'))
    ->assertSee('Pastel de Nata')
    ->assertSee('Carne Louca')
    ->assertDontSee('Francesinha vegana')
    ->set('perPage', '50')
    ->assertSee('Pastel de Nata')
    ->assertSee('Carne Louca')
    ->assertDontSee('Francesinha vegana');

it('properly filters by "chef name is NOT empty"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('', 'is_not_empty', 'chef_name'))
    ->assertSee('Francesinha vegana')
    ->assertDontSee('Pastel de Nata')
    ->assertDontSee('Carne Louca')
    ->assertSee('Francesinha vegana')
    ->assertDontSee('Pastel de Nata')
    ->assertDontSee('Carne Louca');
