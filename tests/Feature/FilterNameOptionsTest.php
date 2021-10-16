<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('properly filters by "name is"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Francesinha', 'is'))
    ->assertSeeHtml('Francesinha')
    ->assertDontSeeHtml('Francesinha vegana');

it('properly filters by "name is" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent dish', 'is'))
    ->assertSeeHtml('No records found')
    ->assertDontSeeHtml('Francesinha');

it('properly filters by "name is not"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Francesinha vegana', 'is_not'))
    ->assertSeeHtml('Francesinha')
    ->assertDontSeeHtml('Francesinha vegana');

it('properly filters by "name is not" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent dish', 'is_not'))
    ->assertSeeHtml('Francesinha')
    ->assertSeeHtml('Francesinha vegana');

it('properly filters by "name contains"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('francesinha', 'contains'))
    ->assertSeeHtml('Francesinha')
    ->assertSeeHtml('Francesinha vegana');

it('properly filters by "name contains" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent dish', 'contains'))
    ->assertSeeHtml('No records found')
    ->assertDontSeeHtml('Francesinha')
    ->assertDontSeeHtml('Francesinha vegana');

it('properly filters by "name contains not"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('francesinha', 'contains_not'))
    ->assertDontSeeHtml('Francesinha')
    ->assertDontSeeHtml('Francesinha vegana');

it('properly filters by "name contains not" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent dish', 'contains_not'))
    ->assertSeeHtml('Francesinha')
    ->assertSeeHtml('Francesinha vegana');

it('properly filters by "name starts with"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('fran', 'starts_with'))
    ->assertSeeHtml('Francesinha')
    ->assertSeeHtml('Francesinha vegana')
    ->assertDontSeeHtml('Barco-Sushi da Sueli');

it('properly filters by "name starts with" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent', 'starts_with'))
    ->assertSeeHtml('No records found')
    ->assertDontSeeHtml('Francesinha')
    ->assertDontSeeHtml('Francesinha vegana')
    ->assertDontSeeHtml('Barco-Sushi da Sueli');

it('properly filters by "name ends with"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('vegana', 'ends_with'))
    ->assertSeeHtml('Francesinha vegana')
    ->assertDontSeeHtml('Barco-Sushi da Sueli');

it('properly filters by "name ends with" using nonexistent record')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputText('Nonexistent', 'ends_with'))
    ->assertSeeHtml('No records found')
    ->assertDontSeeHtml('Francesinha')
    ->assertDontSeeHtml('Francesinha vegana')
    ->assertDontSeeHtml('Barco-Sushi da Sueli');

function filterInputText(string $text, string $type): array
{
    return [
        "input_text" => [
            "name" => $text,
        ],
        "input_text_options" => [
            "name" => $type,
        ],
    ];
}
