<?php

use function Pest\Livewire\livewire;

it('properly filters by bool true', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSee('Em Estoque')
        ->assertSeeHtml('wire:input.debounce.300ms="filterBoolean(\'in_stock\', $event.target.value, \'Em Estoque\')"')
        ->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertSee('Francesinha vegana')
        ->assertDontSeeHtml('Francesinha </div>')
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertDontSee('Barco-Sushi Simples')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('борщ')
        ->call('clearFilter', 'in_stock')
        ->assertSee('Francesinha')
        ->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ');
})->with('themes');

it('properly filters by bool true - using collection', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.debounce.300ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"')
        ->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 4')
        ->assertDontSee('Name 3')
        ->assertDontSee('Name 5')
        ->call('clearFilter', 'in_stock')
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');
})->with('themes with collection table');

it('properly filters by bool false', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterBoolean('in_stock', 'false'))
        ->assertSee('Francesinha')
        ->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê')
        ->assertDontSee('Francesinha vegana')
        ->call('clearFilter', 'in_stock')
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertSee('Francesinha vegana');
})->with('themes');

it('properly filters by bool false - using collection', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.debounce.300ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"')
        ->set('filters', filterBoolean('in_stock', 'false'))
        ->assertSee('Name 3')
        ->assertSee('Name 5')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 4')
        ->call('clearFilter', 'in_stock')
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');
})->with('themes with collection table');

it('properly filters by bool "all"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterBoolean('in_stock', 'all'))
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
})->with('themes');

it('properly filters by bool "all" - using collection', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterBoolean('in_stock', 'all'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');
})->with('themes with collection table');

function filterBoolean(string $field, ?string $value): array
{
    return [
        'boolean' => [
            $field => $value,
        ],
    ];
}
