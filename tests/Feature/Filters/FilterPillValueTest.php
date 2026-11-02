<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

uses()->group('filters', 'enabled-filters');

function pillValueComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'filter-pill-value';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Cheap Dish', 'price' => 10, 'in_stock' => true, 'category_id' => 1, 'created_at' => '2026-01-10'],
                ['id' => 2, 'name' => 'Pricey Dish', 'price' => 500, 'in_stock' => false, 'category_id' => 2, 'created_at' => '2026-03-10'],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
                Filter::boolean('in_stock')->label('Available', 'Sold out'),
                Filter::select('category_id')
                    ->dataSource(fn () => [
                        ['id' => 1, 'title' => 'Soups'],
                        ['id' => 2, 'title' => 'Desserts'],
                    ])
                    ->optionValue('id')
                    ->optionLabel('title'),
                Filter::multiSelect('tags')
                    ->dataSource(collect([
                        ['id' => 'v', 'title' => 'Vegan'],
                        ['id' => 'g', 'title' => 'Gluten free'],
                    ]))
                    ->optionValue('id')
                    ->optionLabel('title'),
                Filter::datepicker('created_at'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Price', 'price'),
                Column::make('In stock', 'in_stock'),
                Column::make('Category', 'category_id'),
                Column::make('Tags', 'tags'),
                Column::make('Created', 'created_at'),
            ];
        }
    };
}

function pillValues(mixed $test): array
{
    return collect($test->get('enabledFilters'))->mapWithKeys(fn ($pill) => [$pill['field'] => $pill['value'] ?? null])->all();
}

it('does not add the filter value to the pill by default', function () {
    $test = Livewire::test(pillValueComponent()::class)
        ->set('filters', ['name' => ['type' => 'input_text', 'value' => 'Cheap', 'op' => 'contains']]);

    expect($test->get('enabledFilters'))->toHaveCount(1)
        ->and($test->get('enabledFilters.0'))->not->toHaveKey('value');

    $test->assertDontSee('Name: Cheap');
});

it('shows the selected value of each filter type in its pill when enabled', function () {
    config(['livewire-powergrid.filter_pill_value' => true]);

    $test = Livewire::test(pillValueComponent()::class)
        ->set('filters', [
            'name' => ['type' => 'input_text', 'value' => 'Cheap', 'op' => 'contains'],
            'price' => ['type' => 'number', 'value' => ['start' => '5', 'end' => '50']],
            'in_stock' => ['type' => 'boolean', 'value' => 'false'],
            'category_id' => ['type' => 'select', 'value' => '2'],
            'tags' => ['type' => 'multi_select', 'value' => ['v', 'g']],
            'created_at' => ['type' => 'date', 'value' => ['formatted' => '2026-01-01 to 2026-02-01']],
        ]);

    expect(pillValues($test))->toBe([
        'name' => 'Cheap',
        'price' => '5 - 50',
        'in_stock' => 'Sold out',
        'category_id' => 'Desserts',
        'tags' => 'Vegan, Gluten free',
        'created_at' => '2026-01-01 to 2026-02-01',
    ]);

    $test->assertSee('Name: Cheap')
        ->assertSee('Category: Desserts');
});

it('formats open number ranges and valueless operators', function () {
    config(['livewire-powergrid.filter_pill_value' => true]);

    $test = Livewire::test(pillValueComponent()::class)
        ->set('filters', [
            'name' => ['type' => 'input_text', 'value' => '', 'op' => 'is_empty'],
            'price' => ['type' => 'number', 'value' => ['start' => '', 'end' => '50']],
        ]);

    expect(pillValues($test))->toBe([
        'name' => 'Is empty',
        'price' => '≤ 50',
    ]);
});

it('keeps resolving select option labels after the inline filters were resolved', function () {
    config(['livewire-powergrid.filter_pill_value' => true]);

    $test = Livewire::test(pillValueComponent()::class)
        ->set('filters', ['category_id' => ['type' => 'select', 'value' => '1']])
        ->set('filters', ['category_id' => ['type' => 'select', 'value' => '2']]);

    expect($test->get('inlineFiltersResolved'))->toBeTrue()
        ->and(pillValues($test))->toBe(['category_id' => 'Desserts']);
});
