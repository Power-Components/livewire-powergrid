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
    expect($qs)->toHaveKey('filters.input_text.name')
        ->and($qs)->toHaveKey('filters.input_text_options.name')
        // number → start + end bindings
        ->and($qs)->toHaveKey('filters.number.price.start')
        ->and($qs)->toHaveKey('filters.number.price.end')
        // select / boolean → the "default" branch key shape
        ->and($qs)->toHaveKey('filters.select.category_id')
        ->and($qs)->toHaveKey('filters.boolean.in_stock')
        // dynamic → binds the wire:model target
        ->and($qs)->toHaveKey('filters.dynamic.slug');

    expect($qs['filters.input_text.name']['as'])->toBe('name');
});

it('prefixes the query string aliases when a prefix is given', function () {
    $qs = queryStringComponent()->buildQueryString('grid');

    expect($qs['filters.input_text.name']['as'])->toBe('grid_name')
        ->and($qs['filters.number.price.start']['as'])->toBe('grid_price_start');
});

it('marks a filter as enabled when its value is present in the request', function () {
    request()->merge([
        'name' => 'Pastel',   // input_text present
        'price_start' => '5',        // number start present
        'calories_end' => '900',      // number end present (start absent)
    ]);

    $component = queryStringComponent();
    $component->buildQueryString();

    $fields = collect($component->enabledFilters)->pluck('field');

    expect($fields)->toContain('name')
        ->and($fields)->toContain('price_start')
        ->and($fields)->toContain('calories_end');

    request()->replace([]);
});

it('lists the columns available for the query string', function () {
    $columns = queryStringComponent()->listColumnForQueryString();

    expect($columns)->toHaveKey('name')
        ->and($columns)->toHaveKey('price')
        ->and($columns->get('name'))->toBe('Name');
});
