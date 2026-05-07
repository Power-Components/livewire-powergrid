<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;

it('applies default boolean filter value "true" on mount', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-default-boolean';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1', 'in_stock' => true]]);
        }

        public function filters(): array
        {
            return [Filter::boolean('in_stock')->default('true')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->assertSet('filters.boolean.in_stock', 'true');
});

it('applies default select filter value on mount', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-default-select';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1', 'category_id' => 1]]);
        }

        public function filters(): array
        {
            return [
                Filter::select('category_id')->dataSource(collect([['id' => 1, 'name' => 'Cat 1']]))->optionValue('id')->optionLabel('name')->default(1),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->assertSet('filters.select.category_id', 1);
});

it('applies default multi_select filter value on mount', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-default-multi-select';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1', 'category_id' => 1]]);
        }

        public function filters(): array
        {
            return [
                Filter::multiSelect('category_id')->dataSource(collect([['id' => 1, 'name' => 'Cat 1']]))->optionValue('id')->optionLabel('name')->default([1]),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->assertSet('filters.multi_select.category_id', [1]);
});

it('applies default input_text filter value on mount', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-default-input-text';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Pastel']]);
        }

        public function filters(): array
        {
            return [Filter::inputText('name')->default('Pastel')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->assertSet('filters.input_text.name', 'Pastel');
});

it('applies default number filter value on mount', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-default-number';

        public function datasource()
        {
            return collect([['id' => 1, 'price' => 10]]);
        }

        public function filters(): array
        {
            return [Filter::number('price')->default(['start' => 10, 'end' => 20])];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('price');
        }

        public function columns(): array
        {
            return [Column::make('Price', 'price')];
        }
    };

    Livewire::test($component::class)
        ->assertSet('filters.number.price.start', 10)
        ->assertSet('filters.number.price.end', 20);
});

it('applies default date filter value on mount', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-default-date';

        public function datasource()
        {
            return collect([['id' => 1, 'date' => '2021-01-01']]);
        }

        public function filters(): array
        {
            return [
                Filter::datepicker('date')->default([
                    'start' => '2021-01-01',
                    'end' => '2021-01-02',
                    'formatted' => '2021-01-01 to 2021-01-02',
                ]),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('date');
        }

        public function columns(): array
        {
            return [Column::make('Date', 'date')];
        }
    };

    Livewire::test($component::class)
        ->assertSet('filters.date.date.start', '2021-01-01')
        ->assertSet('filters.date.date.end', '2021-01-02');
});
