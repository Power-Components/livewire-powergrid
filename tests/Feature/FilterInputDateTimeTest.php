<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('properly filters by "between date"')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputDate('2021-01-01', '2021-03-03'))
    ->assertSeeHtmlInOrder([
        'id="input_produced_at_formatted"',
        'data-field="produced_at"',
        'data-key="enabledFilters.date_picker.produced_at"',
        'type="text"',
        'placeholder="Select a period"',
        'wire:model="filters.input_date_picker.produced_at"',
        'wire:ignore',
    ])
    ->assertSeeHtml('Pastel de Nata')
    ->assertSeeHtml('Peixada da chef Nábia')
    ->assertSeeHtml('Carne Louca')
    ->assertDontSeeHtml('Barco-Sushi Simples')
    ->set('filters', filterInputDate('2021-04-04', '2021-07-07 19:59:58'))
    ->assertSeeHtml('Bife à Rolê')
    ->assertSeeHtml('Francesinha vegana')
    ->assertSeeHtml('Francesinha')
    ->assertDontSeeHtml('Barco-Sushi da Sueli');

it('properly filters by "between date" using incorrect filter')
    ->livewire(DishesTable::class)
    ->set('filters', filterInputDate('2021-03-03', '2021-03-01'))
    ->assertSeeHtml('No records found');

function filterInputDate(string $startDate, string $endDate): array
{
    return [
        "date_picker" => [
            "produced_at" => [
                0 => $startDate,
                1 => $endDate,
            ],
        ],
    ];
}
