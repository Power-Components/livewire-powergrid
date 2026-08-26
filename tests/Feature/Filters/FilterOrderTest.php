<?php

use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

uses()->group('filters');

function orderComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'filter-order') {}

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish', 'price' => 10, 'in_stock' => true],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price')->order(3),
                Filter::boolean('in_stock'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')->add('name')->add('price')->add('in_stock');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Price', 'price'),
                Column::make('In Stock', 'in_stock'),
            ];
        }
    };
}

it('orders dropdown filters by Filter::order, falling back to the filters() index', function () {
    Config::set('livewire-powergrid.filter', 'dropdown');

    $html = Livewire::test(orderComponent('order-dropdown')::class)->html();

    expect($html)->toContain('data-cy="filter-dropdown-panel"')
        ->toMatch('/draftFilters\.input_text\.name[\s\S]*draftFilters\.boolean\.in_stock[\s\S]*draftFilters\.number\.price/');
});

it('orders flyout filters by Filter::order, falling back to the filters() index', function () {
    Config::set('livewire-powergrid.filter', 'flyout');

    $html = Livewire::test(orderComponent('order-flyout')::class)->html();

    expect($html)->toContain('data-cy="filter-flyout"')
        ->toMatch('/draftFilters\.input_text\.name[\s\S]*draftFilters\.boolean\.in_stock[\s\S]*draftFilters\.number\.price/');
});

it('does not reorder inline filters when Filter::order is set', function () {
    Config::set('livewire-powergrid.filter', 'inline');

    $html = Livewire::test(orderComponent('order-inline')::class)->html();

    expect($html)
        ->not->toContain('data-cy="filter-dropdown-panel"')
        ->and($html)->not->toContain('data-cy="filter-flyout"')
        ->and($html)->toMatch('/column-filter-name[\s\S]*column-filter-price[\s\S]*column-filter-in_stock/');
});
