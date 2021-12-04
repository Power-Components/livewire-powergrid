<?php

use function Pest\Livewire\livewire;

it('properly filters by "name is"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Francesinha', 'is', $params->field))
        ->assertSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->call('clearFilter', 'name')
        ->assertSee('Francesinha vegana');
})->with('themes with name field');

it('properly filters by "name is" using nonexistent record', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'is', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha');
})->with('themes with name field');

it('properly filters by "name is not"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Francesinha vegana', 'is_not', $params->field))
        ->assertSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->call('clearFilter', 'name')
        ->assertSee('Francesinha vegana');
})->with('themes with name field');

it('properly filters by "name is not" using nonexistent record', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'is_not', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana')
        ->call('clearFilter', 'name')
        ->assertViewHas('filters', []);
})->with('themes with name field');

it('properly filters by "name contains"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('francesinha', 'contains', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');
})->with('themes with name field');

it('properly filters by "name contains" using nonexistent record', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'contains', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana');
})->with('themes with name field');

it('properly filters by "name contains not"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('francesinha', 'contains_not', $params->field))
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana');
})->with('themes with name field');

it('properly filters by "name contains not" using nonexistent record', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'contains_not', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');
})->with('themes with name field');

it('properly filters by "name starts with"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('fran', 'starts_with', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');
})->with('themes with name field');

it('properly filters by "name starts with" using nonexistent record', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent', 'starts_with', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');
})->with('themes with name field');

it('properly filters by "name ends with"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('vegana', 'ends_with', $params->field))
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');
})->with('themes with name field');

it('properly filters by "name ends with" using nonexistent record', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent', 'ends_with', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');
})->with('themes with name field');

it('properly filters by "chef name is blank"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_blank', 'chef_name'))
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->set('perPage', '50')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana');
})->with('themes with name field');

it('properly filters by "chef name is NOT blank"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_not_blank', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Carne Louca')
        ->set('perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Carne Louca');
})->with('themes');

it('properly filters by "chef name is null"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_null', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Carne Louca')
        ->set('perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Carne Louca');
})->with('themes');

it('properly filters by "chef name is NOT null"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_not_null', 'chef_name'))
        ->assertSee('Francesinha vegana')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->set('perPage', '50')
        ->assertSee('Francesinha vegana')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata');
})->with('themes');

it('properly filters by "chef name is empty"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_empty', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Francesinha vegana')
        ->set('perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Francesinha vegana');
})->with('themes');

it('properly filters by "chef name is NOT empty"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_not_empty', 'chef_name'))
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Carne Louca')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Carne Louca');
})->with('themes');
