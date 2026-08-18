<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\{Category, Dish};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

uses()->group('datasource', 'builders', 'select');

beforeEach(function () {
    TestDatabase::up();
});

it('filters database records with select filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-select-builder-basic';

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
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('category_id');
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

it('filters database records with select filter using different category', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-select-builder-category';

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
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('category_id');
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
        ->set('filters.select.category_id', 1) // Carnes
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata');
});

it('shows all records when select filter is empty', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-select-builder-empty';

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
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('category_id');
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
        ->set('filters.select.category_id', '')
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia');
});

it('filters database records with custom builder logic', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-select-builder-custom';

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
                    ->optionLabel('name')
                    ->builder(function ($builder, $value) {
                        $builder->where('price', '>', 20);
                    }),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('category_id')
                ->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Category', 'category_id'),
                Column::make('Price', 'price'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.select.category_id', 1)
        ->assertSee('Peixada da chef Nábia') // price 20.50
        ->assertSee('Carne Louca') // price 30.00
        ->assertDontSee('Pastel de Nata'); // price 10.00
});

it('filters collection records with select filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-select-builder-collection';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1', 'category_id' => 1],
                ['id' => 2, 'name' => 'Item 2', 'category_id' => 2],
                ['id' => 3, 'name' => 'Item 3', 'category_id' => 1],
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
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('category_id');
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
        ->assertSee('Item 1')
        ->assertSee('Item 3')
        ->assertDontSee('Item 2');
});

it('filters collection with custom collection logic', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-select-builder-collection-custom';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1', 'category_id' => 1],
                ['id' => 2, 'name' => 'Item 2', 'category_id' => 2],
                ['id' => 3, 'name' => 'Item 3', 'category_id' => 1],
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
                    ->optionLabel('name')
                    ->collection(function ($collection, $value) {
                        return $collection->where('id', '>', 1);
                    }),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('category_id');
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
        ->assertDontSee('Item 1')
        ->assertSee('Item 2')
        ->assertSee('Item 3');
});

it('returns all collection records when select value is empty', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-select-builder-collection-empty';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1', 'category_id' => 1],
                ['id' => 2, 'name' => 'Item 2', 'category_id' => 2],
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
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('category_id');
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
        ->set('filters.select.category_id', '')
        ->assertSee('Item 1')
        ->assertSee('Item 2');
});
