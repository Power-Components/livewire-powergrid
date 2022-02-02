<?php

use function Pest\Livewire\livewire;

it('properly filters by "between date"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputDate('2021-01-01', '2021-03-03'))
        ->assertSeeHtmlInOrder([
            'id="input_produced_at_formatted"',
            'type="text"',
            'placeholder="Select a period"',
            'wire:model="filters.input_date_picker.produced_at"',
        ])
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertDontSee('Barco-Sushi Simples')
        ->set('filters', filterInputDate('2021-04-04', '2021-07-07 19:59:58'))
        ->assertSee('Bife à Rolê')
        ->assertSee('Francesinha vegana')
        ->assertSee('Francesinha')
        ->assertDontSeeHtml('Barco-Sushi da Sueli');
})->with('themes');

it('properly filters by "between date" using incorrect filter', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
    ->set('filters', filterInputDate('2021-03-03', '2021-03-01'))
    ->assertSee('No records found');
})->with('themes');

function filterInputDate(string $startDate, string $endDate): array
{
    return [
        'date_picker' => [
            'produced_at' => [
                0 => $startDate,
                1 => $endDate,
            ],
        ],
    ];
}
