<?php

use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

uses()->group('filters', 'querystring');

function queryStringComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'querystring';

        // Expose the protected query-string builder so we can exercise every branch.
        public function buildQueryString(string $prefix = ''): array
        {
            return $this->powerGridQueryString($prefix);
        }

        public function datasource()
        {
            return collect([]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
                Filter::number('calories'),
                Filter::boolean('in_stock'),
                Filter::select('category_id')
                    ->dataSource(collect([]))
                    ->optionValue('category_id')
                    ->optionLabel('name'),
                Filter::dynamic('slug', 'field')
                    ->attributes(['wire:model' => 'filters.dynamic.slug']),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Price', 'price'),
                Column::make('Calories', 'calories'),
                Column::make('Stock', 'in_stock'),
                Column::make('Category', 'category_id'),
                Column::make('Slug', 'slug'),
            ];
        }
    };
}

it('builds query string bindings for every filter type', function () {
    $qs = queryStringComponent()->buildQueryString();

    // input_text → value + operator bindings
    expect($qs)->toHaveKey('filters.name.value')
        ->and($qs)->toHaveKey('filters.name.op')
        // number → start + end bindings
        ->and($qs)->toHaveKey('filters.price.value.start')
        ->and($qs)->toHaveKey('filters.price.value.end')
        // select / boolean → the "default" branch key shape
        ->and($qs)->toHaveKey('filters.category_id.value')
        ->and($qs)->toHaveKey('filters.in_stock.value')
        // dynamic → binds the wire:model target
        ->and($qs)->toHaveKey('filters.dynamic.slug');

    expect($qs['filters.name.value']['as'])->toBe('name');
});

it('prefixes the query string aliases when a prefix is given', function () {
    $qs = queryStringComponent()->buildQueryString('grid');

    expect($qs['filters.name.value']['as'])->toBe('grid_name')
        ->and($qs['filters.price.value.start']['as'])->toBe('grid_price_start');
});

it('does not mutate enabled filters when building the query string', function () {
    request()->merge([
        'name' => 'Pastel',
        'price_start' => '5',
        'calories_end' => '900',
    ]);

    $component = queryStringComponent();
    $component->buildQueryString();

    expect($component->enabledFilters)->toBeEmpty();

    request()->replace([]);
});

it('lists the columns available for the query string', function () {
    $columns = queryStringComponent()->listColumnForQueryString();

    expect($columns)->toHaveKey('name')
        ->and($columns)->toHaveKey('price')
        ->and($columns->get('name'))->toBe('Name');
});
