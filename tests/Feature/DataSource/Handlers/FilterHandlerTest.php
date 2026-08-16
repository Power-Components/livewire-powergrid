<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\{Category, Dish};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

uses()->group('datasource', 'handlers', 'filter');

beforeEach(function () {
    TestDatabase::up();
});

it('applies no filters when filter definitions are empty', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-empty';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::multiSelect('category_id')
                    ->dataSource(Category::all())
                    ->optionValue('id')
                    ->optionLabel('name'),
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
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia');
});

it('applies select filter through FilterHandler', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-select';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::select('category_id')
                    ->dataSource(Category::all())
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
        ->set('filters.select.category_id', 6) // Sobremesas
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
});

it('applies boolean filter through FilterHandler', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-boolean';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::boolean('in_stock'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('in_stock');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('In Stock', 'in_stock'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.boolean.in_stock', 'false')
        ->assertSee('Francesinha')
        ->assertDontSee('Pastel de Nata');
});

it('applies number filter with start and end through FilterHandler', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-number';

        public function datasource()
        {
            return Dish::query();
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
        ->set('filters.number.price', ['start' => 10, 'end' => 30])
        ->assertSee('Pastel de Nata')  // 10.00
        ->assertSee('Peixada da chef Nábia')  // 20.50
        ->assertSee('Carne Louca')  // 30.00
        ->assertDontSee('Bife à Rolê'); // 40.50
});

it('applies date filter with start and end through FilterHandler', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-date';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::datepicker('produced_at')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('produced_at');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Produced At', 'produced_at'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.date.produced_at', [
            'start' => '2021-01-01',
            'end' => '2021-02-28',
        ])
        ->assertSee('Pastel de Nata')  // 2021-01-01
        ->assertSee('Peixada da chef Nábia')  // 2021-02-02
        ->assertDontSee('Carne Louca'); // 2021-03-03
});

it('applies datetime filter through FilterHandler', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-datetime';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::datetimepicker('produced_at')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('produced_at');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Produced At', 'produced_at'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.datetime.produced_at', [
            'start' => '2021-01-01 00:00:00',
            'end' => '2021-02-28 23:59:59',
        ])
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca');
});

it('applies input_text filter through FilterHandler', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-input-text';

        public function datasource()
        {
            return Dish::query();
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
        ->assertDontSee('Peixada da chef Nábia');
});

it('applies multi_select filter through FilterHandler', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-multiselect';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::multiSelect('category_id')];
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
        ->set('filters.multi_select.category_id', [1, 6]) // Carnes (1), Sobremesas (6)
        ->assertSee('Pastel de Nata')  // category_id = 6
        ->assertSee('Peixada da chef Nábia')  // category_id = 1
        ->assertSee('Carne Louca'); // category_id = 1
});

it('applies multiple filters simultaneously through FilterHandler', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-multiple';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::boolean('in_stock'),
                Filter::number('price'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('in_stock')
                ->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('In Stock', 'in_stock'),
                Column::make('Price', 'price'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.boolean.in_stock', 'true')
        ->set('filters.number.price', ['start' => 10, 'end' => 25])
        ->assertSee('Pastel de Nata')  // in_stock=true, price=10.00
        ->assertSee('Peixada da chef Nábia')  // in_stock=true, price=20.50
        ->assertDontSee('Carne Louca')  // price=30.00 (out of range)
        ->assertDontSee('Francesinha'); // in_stock=false
});

it('handles dotted column names in filters', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-dotted';

        public function datasource()
        {
            return Dish::query();
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
        ->set('filters.number.price.start', 20)
        ->set('filters.number.price.end', 35)
        ->assertSee('Peixada da chef Nábia')  // 20.50
        ->assertSee('Carne Louca')  // 30.00
        ->assertDontSee('Pastel de Nata')  // 10.00
        ->assertDontSee('Bife à Rolê'); // 40.50
});

it('handles numeric indexed arrays in filters', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-indexed';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::multiSelect('category_id')];
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
        ->set('filters.multi_select.category_id', [0 => 1, 1 => 6])
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia');
});

it('returns query unchanged when no filter values are set', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-filter-handler-no-values';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::boolean('in_stock'),
                Filter::number('price'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('in_stock')
                ->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('In Stock', 'in_stock'),
                Column::make('Price', 'price'),
            ];
        }
    };

    $livewire = Livewire::test($component::class);

    // Should show all dishes when no filters are applied
    $livewire->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Francesinha');
});
