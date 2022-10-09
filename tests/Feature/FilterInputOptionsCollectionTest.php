<?php

use function Pest\Livewire\livewire;

it('properly filters by "name is" using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('Name 1', 'is'))
        ->assertSee('Name 1')
        ->assertDontSee('Name 2')
        ->call('clearFilter', 'name')
        ->assertSeeText('Name 1');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name is" using nonexistent record using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('Name 6', 'is'))
        ->assertSee('No records found')
        ->assertDontSee('Name 1');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name is not" using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('Name 2', 'is_not'))
        ->assertSee('Name 1')
        ->assertDontSee('NAme 2')
        ->call('clearFilter', 'name')
        ->assertSee('Name 2');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name is not" using nonexistent record using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('Name 6', 'is_not'))
        ->assertSee('Name 5')
        ->assertSee('Name 4')
        ->call('clearFilter', 'name');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name contains" using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('4', 'contains'))
        ->assertSee('Name 4')
        ->assertDontSee('Name 2');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name contains" using nonexistent record using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('Name 6', 'contains'))
        ->assertSee('No records found')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name contains not" using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('5', 'contains_not'))
        ->assertDontSee('Name 5')
        ->assertSee('Name 1');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name contains not" using nonexistent record using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('Name 6', 'contains_not'))
        ->assertSee('Name 1')
        ->assertSee('Name 2');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name starts with" using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('Na', 'starts_with'))
        ->assertSee('Name 1')
        ->assertSee('Name 2');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name starts with" using nonexistent record using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('Nonexistent', 'starts_with'))
        ->assertSee('No records found')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name ends with" using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('e 5', 'ends_with'))
        ->assertSee('Name 5')
        ->assertDontSee('Name 1');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "name ends with" using nonexistent record using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('Nonexistent', 'ends_with'))
        ->assertSee('No records found')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "chef name is blank"', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('', 'is_blank', 'chef_name'))
        ->assertSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "chef name is NOT blank"', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('', 'is_not_blank', 'chef_name'))
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertDontSee('Name 1')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertDontSee('Name 1');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "chef name is null"', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('', 'is_null', 'chef_name'))
        ->assertSee('Name 2')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 3')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 2')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 3');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "chef name is NOT null"', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('', 'is_not_null', 'chef_name'))
        ->assertSee('Name 1')
        ->assertSee('Name 3')
        ->assertDontSee('Name 2')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 1')
        ->assertSee('Name 3')
        ->assertDontSee('Name 2');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "chef name is empty"', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('', 'is_empty', 'chef_name'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertDontSee('Name 3')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertDontSee('Name 3');
})->with('themes with collection table', 'themes with array table');

it('properly filters by "chef name is NOT empty"', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('filters', filterInputText('', 'is_not_empty', 'chef_name'))
        ->assertSee('Name 3')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Name 3')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2');
})->with('themes with collection table', 'themes with array table');
