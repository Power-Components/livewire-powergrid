<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Filters\Filter;

it('properly filters by "between date"', function (string $component, object $params) {
    $datepicker = Filter::datepicker('produced_at_formatted', 'produced_at');

    livewire($component, [
        'testFilters' => [$datepicker],
    ])
        ->call($params->theme)
        ->set('filters', filterInputDate('2021-01-01', '2021-03-03'))
        ->assertSeeHtmlInOrder([
            'id="input_produced_at_formatted"',
            'type="text"',
            'placeholder="Select a period"',
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
})->group('filters', 'filterDatePicker')->with('themes');

it('properly filters by "between date" using incorrect filter', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
    ->set('filters', filterInputDate('2021-03-03', '2021-03-01'))
    ->assertSee('No records found');
})->group('filters', 'filterDatePicker')->with('themes');

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
