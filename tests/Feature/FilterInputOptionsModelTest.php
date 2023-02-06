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
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Francesinha vegana', 'is_not', $params->field))
        ->assertSee('Francesinha')
        ->assertDontSee('Francesinha vegana');

    expectInputText($params, $component, 'Francesinha vegana', 'is_not');

    $component->call('clearFilter', $params->field)
        ->assertSee('Francesinha vegana');

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name is not" using nonexistent record', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'is_not', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');

    expectInputText($params, $component, 'Nonexistent dish', 'is_not');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name contains"', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('francesinha', 'contains', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');

    expectInputText($params, $component, 'francesinha', 'contains');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name contains" using nonexistent record', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'contains', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana');

    expectInputText($params, $component, 'Nonexistent dish', 'contains');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name contains not"', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('francesinha', 'contains_not', $params->field))
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana');

    expectInputText($params, $component, 'francesinha', 'contains_not');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name contains not" using nonexistent record', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'contains_not', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');

    expectInputText($params, $component, 'Nonexistent dish', 'contains_not');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name starts with"', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('fran', 'starts_with', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expectInputText($params, $component, 'fran', 'starts_with');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name starts with" using nonexistent record', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent', 'starts_with', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expectInputText($params, $component, 'Nonexistent', 'starts_with');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name ends with"', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('vegana', 'ends_with', $params->field))
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expectInputText($params, $component, 'vegana', 'ends_with');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "name ends with" using nonexistent record', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent', 'ends_with', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expectInputText($params, $component, 'Nonexistent', 'ends_with');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "chef name is blank"', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_blank', 'chef_name'))
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana');

    expect($component->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_blank',
            ],
        ]);

    $component->call('clearFilter', 'chef_name');

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes with name field');

it('properly filters by "chef name is NOT blank"', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_not_blank', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Carne Louca')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Carne Louca');

    expect($component->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_not_blank',
            ],
        ]);

    $component->call('clearFilter', 'chef_name');

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes');

it('properly filters by "chef name is null"', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_null', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Carne Louca')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Carne Louca');

    expect($component->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_null',
            ],
        ]);

    $component->call('clearFilter', 'chef_name');

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->with('themes');

it('properly filters by "chef name is NOT null"', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_not_null', 'chef_name'))
        ->assertSee('Francesinha vegana')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Francesinha vegana')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata');

    expect($component->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_not_null',
            ],
        ]);

    $component->call('clearFilter', 'is_not_null');

    expect($component->filters)
        ->toMatchArray([]);
})->with('themes');

it('properly filters by "chef name is empty"', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_empty', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Francesinha vegana')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Francesinha vegana');

    expect($component->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_empty',
            ],
        ]);

    $component->call('clearFilter', 'is_empty');

    expect($component->filters)
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
