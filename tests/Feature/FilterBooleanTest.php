<?php

use function Pest\Livewire\livewire;

it('properly filters by bool true', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->assertSee('Em Estoque')
        ->assertSeeHtml('wire:input.lazy="filterBoolean(\'in_stock\', $event.target.value, \'Em Estoque\')"');

    expect($component->filters)
        ->toBeEmpty();

    $component->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertSee('Francesinha vegana')
        ->assertDontSeeHtml('Francesinha </div>')
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertDontSee('Barco-Sushi Simples')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('борщ');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'true',
            ],
        ]);

    $component->call('clearFilter', 'in_stock');

    expect($component->filters)
        ->toMatchArray([]);

    $component->assertSee('Francesinha')
        ->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ');
})->with('themes');

it('properly filters by bool true - using collection & array table', function (string $component, string $theme) {
    $component = livewire($component)
        ->call($theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.lazy="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)
        ->toBeEmpty();

    $component ->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 4')
        ->assertDontSee('Name 3')
        ->assertDontSee('Name 5');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'true',
            ],
        ]);

    $component->call('clearFilter', 'in_stock');

    $component->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');

    expect($component->filters)
        ->toMatchArray([]);
})->with('themes with collection table', 'themes with array table');

it('properly filters by bool true - using collection', function (string $component, string $theme) {
    $component = livewire($component)
        ->call($theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.lazy="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)
        ->toBeEmpty();

    $component ->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 4')
        ->assertDontSee('Name 3')
        ->assertDontSee('Name 5');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'true',
            ],
        ]);

    $component->call('clearFilter', 'in_stock');

    $component->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');

    expect($component->filters)
        ->toMatchArray([]);
})->with('themes with array table');

it('properly filters by bool false', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme);

    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'false'))
        ->assertSee('Francesinha')
        ->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê')
        ->assertDontSee('Francesinha vegana');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'false',
            ],
        ]);

    $component->call('clearFilter', 'in_stock')
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertSee('Francesinha vegana');

    expect($component->filters)
        ->toMatchArray([]);
})->with('themes');

it('properly filters by bool false - using collection & array', function (string $component, string $theme) {
    $component = livewire($component)
        ->call($theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.lazy="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'false'))
        ->assertSee('Name 3')
        ->assertSee('Name 5')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 4');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'false',
            ],
        ]);

    $component->call('clearFilter', 'in_stock')
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');

    expect($component->filters)
        ->toMatchArray([]);
})->with('themes with collection table', 'themes with array table');

it('properly filters by bool "all"', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme);

    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'all'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertSee('Francesinha vegana')
        ->assertSee('Francesinha')
        ->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'all',
            ],
        ]);
})->with('themes');

it('properly filters by bool "all" - using collection & array table', function (string $component, string $theme) {
    $component = livewire($component)
        ->call($theme);

    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'all'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'all',
            ],
        ]);
})->with('themes with collection table', 'themes with array table');

function filterBoolean(string $field, ?string $value): array
{
    return [
        'boolean' => [
            $field => $value,
        ],
    ];
}
