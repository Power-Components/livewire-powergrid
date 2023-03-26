<?php

use function Pest\Livewire\livewire;

it('properly filter the produced_at field between two dates', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterDate('produced_at', ['2021-02-02 00:00:00', '2021-04-04 00:00:00']))
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertDontSee('Francesinha vegana');
})->with('themes')->exceptSQLite();

it('properly filter the created_at field between two dates using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterDateTime('created_at', ['2021-01-01 00:00:00', '2021-04-04 00:00:00']))
        ->assertSeeText('Name 1')
        ->assertSeeText('Name 2')
        ->assertSeeText('Name 3')
        ->assertSeeText('Name 4')
        ->assertDontSeeHtml('Name 5');
})->with('themes with collection table', 'themes with array table');

it('properly filter the produced_at field between another two dates', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterDate('produced_at', ['2021-11-11 00:00:00', '2021-12-31 00:00:00']))
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê');
})->with('themes');

function filterDateTime(string $dataField, array $value): array
{
    return [
        'datetime' => [
            $dataField => $value,
        ],
    ];
}

function filterDate(string $dataField, array $value): array
{
    return [
        'date' => [
            $dataField => $value,
        ],
    ];
}
