<?php

use function Pest\Livewire\livewire;

it('properly filters by "name is"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Francesinha', 'is', $params->field))
        ->assertSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->call('clearFilter', $params->field)
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
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Francesinha vegana', 'is_not', $params->field))
        ->assertSee('Francesinha')
        ->assertDontSee('Francesinha vegana');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => 'Francesinha vegana',
            ],
            'input_text_options' => [
                $params->field => 'is_not',
            ],
        ]);

    $powergrid->call('clearFilter', $params->field)
        ->assertSee('Francesinha vegana');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name is not" using nonexistent record', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'is_not', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => 'Nonexistent dish',
            ],
            'input_text_options' => [
                $params->field => 'is_not',
            ],
        ]);

    $powergrid->call('clearFilter', $params->field);

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name contains"', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('francesinha', 'contains', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => 'francesinha',
            ],
            'input_text_options' => [
                $params->field => 'contains',
            ],
        ]);

    $powergrid->call('clearFilter', $params->field);

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name contains" using nonexistent record', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'contains', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => 'Nonexistent dish',
            ],
            'input_text_options' => [
                $params->field => 'contains',
            ],
        ]);

    $powergrid->call('clearFilter', $params->field);

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name contains not"', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('francesinha', 'contains_not', $params->field))
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => 'francesinha',
            ],
            'input_text_options' => [
                $params->field => 'contains_not',
            ],
        ]);

    $powergrid->call('clearFilter', $params->field);

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name contains not" using nonexistent record', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'contains_not', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => 'Nonexistent dish',
            ],
            'input_text_options' => [
                $params->field => 'contains_not',
            ],
        ]);

    $powergrid->call('clearFilter', $params->field);

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name starts with"', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('fran', 'starts_with', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => 'fran',
            ],
            'input_text_options' => [
                $params->field => 'starts_with',
            ],
        ]);

    $powergrid->call('clearFilter', $params->field);

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name starts with" using nonexistent record', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent', 'starts_with', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => 'Nonexistent',
            ],
            'input_text_options' => [
                $params->field => 'starts_with',
            ],
        ]);

    $powergrid->call('clearFilter', $params->field);

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name ends with"', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('vegana', 'ends_with', $params->field))
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => 'vegana',
            ],
            'input_text_options' => [
                $params->field => 'ends_with',
            ],
        ]);

    $powergrid->call('clearFilter', $params->field);

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name ends with" using nonexistent record', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent', 'ends_with', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => 'Nonexistent',
            ],
            'input_text_options' => [
                $params->field => 'ends_with',
            ],
        ]);

    $powergrid->call('clearFilter', $params->field);

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "chef name is blank"', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_blank', 'chef_name'))
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->set('perPage', '50')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_blank',
            ],
        ]);

    $powergrid->call('clearFilter', 'chef_name');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "chef name is NOT blank"', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_not_blank', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Carne Louca')
        ->set('perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Carne Louca');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_not_blank',
            ],
        ]);

    $powergrid->call('clearFilter', 'chef_name');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes');

it('properly filters by "chef name is null"', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_null', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Carne Louca')
        ->set('perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Carne Louca');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_null',
            ],
        ]);

    $powergrid->call('clearFilter', 'chef_name');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes');

it('properly filters by "chef name is NOT null"', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_not_null', 'chef_name'))
        ->assertSee('Francesinha vegana')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->set('perPage', '50')
        ->assertSee('Francesinha vegana')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_not_null',
            ],
        ]);

    $powergrid->call('clearFilter', 'is_not_null');

    expect($powergrid->filters)
        ->toMatchArray([]);
})->with('themes');

it('properly filters by "chef name is empty"', function (string $component, object $params) {
    $powergrid = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_empty', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Francesinha vegana')
        ->set('perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Francesinha vegana');

    expect($powergrid->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_empty',
            ],
        ]);

    $powergrid->call('clearFilter', 'is_empty');

    expect($powergrid->filters)
        ->toMatchArray([]);
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
