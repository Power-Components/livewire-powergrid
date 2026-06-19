<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;

uses()->group('datasource', 'collection');

it('searches in collection with empty search term', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-search-empty';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel de Nata'],
                ['id' => 2, 'name' => 'Peixada'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')->searchable()];
        }
    };

    Livewire::test($component::class)
        ->set('search', '')
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada');
});

it('searches in collection with valid search term', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-search-valid';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel de Nata'],
                ['id' => 2, 'name' => 'Peixada da chef'],
                ['id' => 3, 'name' => 'Carne Louca'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')->searchable()];
        }
    };

    Livewire::test($component::class)
        ->set('search', 'Pastel')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada')
        ->assertDontSee('Carne Louca');
});

it('searches in collection with case-insensitive term', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-search-case';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'PASTEL DE NATA'],
                ['id' => 2, 'name' => 'peixada da chef'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')->searchable()];
        }
    };

    Livewire::test($component::class)
        ->set('search', 'pastel')
        ->assertSee('PASTEL DE NATA')
        ->assertDontSee('peixada');
});

it('filters collection with select filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-filter-select';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1],
                ['id' => 2, 'name' => 'Dish 2', 'category_id' => 2],
                ['id' => 3, 'name' => 'Dish 3', 'category_id' => 1],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::select('category_id')
                    ->dataSource(collect([
                        ['id' => 1, 'name' => 'Category 1'],
                        ['id' => 2, 'name' => 'Category 2'],
                    ]))
                    ->optionValue('id')
                    ->optionLabel('name'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('category_id');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Category', 'category_id'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.select.category_id', 1)
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2')
        ->assertSee('Dish 3');
});

it('filters collection with boolean filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-filter-boolean';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Active Item', 'is_active' => true],
                ['id' => 2, 'name' => 'Inactive Item', 'is_active' => false],
                ['id' => 3, 'name' => 'Another Active', 'is_active' => true],
            ]);
        }

        public function filters(): array
        {
            return [Filter::boolean('is_active')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('is_active');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Active', 'is_active'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.boolean.is_active', 'true')
        ->assertSee('Active Item')
        ->assertDontSee('Inactive Item')
        ->assertSee('Another Active');
});

it('filters collection with multi_select filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-filter-multiselect';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1],
                ['id' => 2, 'name' => 'Dish 2', 'category_id' => 2],
                ['id' => 3, 'name' => 'Dish 3', 'category_id' => 3],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::multiSelect('category_id')
                    ->dataSource(collect([
                        ['id' => 1, 'name' => 'Category 1'],
                        ['id' => 2, 'name' => 'Category 2'],
                        ['id' => 3, 'name' => 'Category 3'],
                    ]))
                    ->optionValue('id')
                    ->optionLabel('name'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('category_id');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Category', 'category_id'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.multi_select.category_id', [1, 2])
        ->assertSee('Dish 1')
        ->assertSee('Dish 2')
        ->assertDontSee('Dish 3');
});

it('filters collection with number filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-filter-number';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1', 'price' => 10],
                ['id' => 2, 'name' => 'Item 2', 'price' => 20],
                ['id' => 3, 'name' => 'Item 3', 'price' => 30],
                ['id' => 4, 'name' => 'Item 4', 'price' => 40],
            ]);
        }

        public function filters(): array
        {
            return [Filter::number('price')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Price', 'price'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.number.price', ['start' => 15, 'end' => 35])
        ->assertDontSee('Item 1')
        ->assertSee('Item 2')
        ->assertSee('Item 3')
        ->assertDontSee('Item 4');
});

it('filters collection with input_text filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-filter-input-text';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel de Nata'],
                ['id' => 2, 'name' => 'Peixada da chef'],
                ['id' => 3, 'name' => 'Carne Louca'],
            ]);
        }

        public function filters(): array
        {
            return [Filter::inputText('name')];
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
        ->set('filters.input_text.name', 'Pastel')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada')
        ->assertDontSee('Carne Louca');
});

it('filters collection with date filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-filter-date';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Event 1', 'date' => '2024-01-01'],
                ['id' => 2, 'name' => 'Event 2', 'date' => '2024-02-15'],
                ['id' => 3, 'name' => 'Event 3', 'date' => '2024-03-30'],
            ]);
        }

        public function filters(): array
        {
            return [Filter::datepicker('date')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('date');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Date', 'date'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.date.date', ['start' => '2024-02-01', 'end' => '2024-03-01'])
        ->assertDontSee('Event 1')
        ->assertSee('Event 2')
        ->assertDontSee('Event 3');
});

it('filters collection with datetime filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-filter-datetime';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Event 1', 'created_at' => '2024-01-01 10:00:00'],
                ['id' => 2, 'name' => 'Event 2', 'created_at' => '2024-02-15 14:30:00'],
                ['id' => 3, 'name' => 'Event 3', 'created_at' => '2024-03-30 08:45:00'],
            ]);
        }

        public function filters(): array
        {
            return [Filter::datetimepicker('created_at')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('created_at');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Created At', 'created_at'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.datetime.created_at', ['start' => '2024-02-01 00:00:00', 'end' => '2024-03-01 23:59:59'])
        ->assertDontSee('Event 1')
        ->assertSee('Event 2')
        ->assertDontSee('Event 3');
});

it('filters collection with no filters returns all items', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-no-filters';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1'],
                ['id' => 2, 'name' => 'Item 2'],
            ]);
        }

        public function filters(): array
        {
            return [];
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
        ->assertSee('Item 1')
        ->assertSee('Item 2');
});

it('uses filterContains for searchable columns', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-filter-contains';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel de Nata', 'description' => 'Sweet pastry'],
                ['id' => 2, 'name' => 'Peixada', 'description' => 'Fish stew'],
                ['id' => 3, 'name' => 'Carne Louca', 'description' => 'Meat dish'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('description');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name')->searchable(),
                Column::make('Description', 'description')->searchable(),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('search', 'Fish')
        ->assertDontSee('Pastel de Nata')
        ->assertSee('Peixada')
        ->assertDontSee('Carne Louca');
});

it('filterContains returns all items with empty search', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-filter-contains-empty';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1'],
                ['id' => 2, 'name' => 'Item 2'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')->searchable()];
        }
    };

    Livewire::test($component::class)
        ->set('search', '')
        ->assertSee('Item 1')
        ->assertSee('Item 2');
});
