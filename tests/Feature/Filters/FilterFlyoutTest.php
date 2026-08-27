<?php

use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

uses()->group('filters', 'filter-flyout');

function flyoutComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'filter-flyout') {}

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Cheap Dish', 'price' => 10],
                ['id' => 2, 'name' => 'Mid Dish', 'price' => 50],
                ['id' => 3, 'name' => 'Expensive Dish', 'price' => 500],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Price', 'price'),
            ];
        }
    };
}

beforeEach(fn () => Config::set('livewire-powergrid.filter', 'flyout'));

it('renders reset and apply but no clear all button in the flyout', function () {
    $html = Livewire::test(flyoutComponent('flyout-html')::class)->html();

    expect($html)->toContain('data-cy="filter-flyout-reset"')
        ->and($html)->toContain('data-cy="filter-flyout-apply"')
        ->and($html)->not->toContain('data-cy="filter-flyout-clear-all"')
        ->and($html)->not->toContain('draftFilters.input_text.name');
});

it('reset restores the draft to the applied filters and keeps results', function () {
    $test = Livewire::test(flyoutComponent('flyout-reset')::class)
        ->set('draftFilters.input_text.name', 'Expensive')
        ->call('applyFilters')
        ->set('draftFilters.input_text.name', 'Cheap')
        ->call('resetFilters');

    expect($test->get('draftFilters'))->toBe(['input_text' => ['name' => 'Expensive']])
        ->and($test->get('filters'))->toBe(['input_text' => ['name' => 'Expensive']]);

    $test->assertSee('Expensive Dish')
        ->assertDontSee('Cheap Dish');
});

it('reset asks the widgets to restore from state instead of wiping them', function () {
    Livewire::test(flyoutComponent('flyout-restore')::class)
        ->set('draftFilters.input_text.name', 'Dish')
        ->call('applyFilters')
        ->call('resetFilters')
        ->assertDispatched('pg:restore_flatpickr::filter-flyout')
        ->assertDispatched('pg:restore_multi_select::filter-flyout')
        ->assertNotDispatched('pg:clear_all_flatpickr::filter-flyout')
        ->assertNotDispatched('pg:clear_all_multi_select::filter-flyout');
});
