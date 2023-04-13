<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Filters\Filter;

use PowerComponents\LivewirePowerGrid\Tests\{DishesArrayTable, DishesCollectionTable};

it(
    'properly filters by "name is" using collection & array table',
    function (string $component, string $theme) {
        $filter   = Filter::inputText('name', 'name')->operators();
        $livewire = livewire($component, [
            'testFilters' => [$filter],
        ])
            ->call($theme)
            ->set('filters', filterInputText('Name 1', 'is'))
            ->assertSee('Name 1')
            ->assertDontSee('Name 2')
            ->call('clearFilter', 'name')
            ->assertSeeText('Name 1');

        expectColumnsFilterMatch($livewire, $filter);
    }
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name is" using nonexistent record using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Name 6', 'is'))
        ->assertSee('No records found')
        ->assertDontSee('Name 1')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name is not" using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Name 2', 'is_not'))
        ->assertSee('Name 1')
        ->assertDontSee('NAme 2')
        ->call('clearFilter', 'name')
        ->assertSee('Name 2')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name is not" using nonexistent record using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Name 6', 'is_not'))
        ->assertSee('Name 5')
        ->assertSee('Name 4')
        ->call('clearFilter', 'name')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name contains" using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('4', 'contains'))
        ->assertSee('Name 4')
        ->assertDontSee('Name 2')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name contains" using nonexistent record using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Name 6', 'contains'))
        ->assertSee('No records found')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name contains not" using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('5', 'contains_not'))
        ->assertDontSee('Name 5')
        ->assertSee('Name 1')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name contains not" using nonexistent record using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Name 6', 'contains_not'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name starts with" using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Na', 'starts_with'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name starts with" using nonexistent record using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Nonexistent', 'starts_with'))
        ->assertSee('No records found')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name ends with" using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('e 5', 'ends_with'))
        ->assertSee('Name 5')
        ->assertDontSee('Name 1')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "name ends with" using nonexistent record using collection & array table',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Nonexistent', 'ends_with'))
        ->assertSee('No records found')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "chef name is blank"',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('', 'is_blank', 'chef_name'))
        ->assertSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "chef name is NOT blank"',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('', 'is_not_blank', 'chef_name'))
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertDontSee('Name 1')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertDontSee('Name 1')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "chef name is null"',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('', 'is_null', 'chef_name'))
        ->assertSee('Name 2')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 3')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 2')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 3')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "chef name is NOT null"',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('', 'is_not_null', 'chef_name'))
        ->assertDontSee('Name 1')
        ->assertSee('Name 3')
        ->assertDontSee('Name 2')
        ->set('setUp.footer.perPage', '50')
        ->assertDontSee('Name 1')
        ->assertSee('Name 3')
        ->assertDontSee('Name 2')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "chef name is empty"',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('', 'is_empty', 'chef_name'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertDontSee('Name 3')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertDontSee('Name 3')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

it(
    'properly filters by "chef name is NOT empty"',
    fn (string $component, string $theme) => livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('', 'is_not_empty', 'chef_name'))
        ->assertSee('Name 3')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 3')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
)->group('filters', 'filterInputText')
    ->with('filter_input_text_options_themes_collection', 'filter_input_text_options_themes_array');

dataset('filter_input_text_options_themes_collection', [
    'tailwind'  => [DishesCollectionTable::class, 'tailwind'],
    'bootstrap' => [DishesCollectionTable::class, 'bootstrap'],
]);

dataset('filter_input_text_options_themes_array', [
    'tailwind'  => [DishesArrayTable::class, 'tailwind'],
    'bootstrap' => [DishesArrayTable::class, 'bootstrap'],
]);
