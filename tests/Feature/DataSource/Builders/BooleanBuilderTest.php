<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

uses()->group('datasource', 'builders', 'boolean');

beforeEach(function () {
    TestDatabase::up();
});

it('filters database records by boolean field with true value', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-boolean-builder-true';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::boolean('in_stock')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('in_stock');
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
        ->set('filters.boolean.in_stock', 'true')
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Barco-Sushi da Sueli');
});

it('filters database records by boolean field with false value', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-boolean-builder-false';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::boolean('in_stock')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('in_stock');
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
        ->assertSee('Barco-Sushi da Sueli')
        ->assertDontSee('Pastel de Nata');
});

it('filters database records by boolean field with all value', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-boolean-builder-all';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::boolean('in_stock')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('in_stock');
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
        ->set('filters.boolean.in_stock', 'all')
        ->assertSee('Pastel de Nata')
        ->assertSee('Francesinha');
});

it('filters database records by boolean field with numeric value 1', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-boolean-builder-numeric-1';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::boolean('in_stock')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('in_stock');
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
        ->set('filters.boolean.in_stock', '1')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Barco-Sushi da Sueli');
});

it('filters database records by boolean field with null value defaults to all', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-boolean-builder-null';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::boolean('in_stock')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('in_stock');
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
        ->set('filters.boolean.in_stock', null)
        ->assertSee('Pastel de Nata')
        ->assertSee('Francesinha');
});

it('filters database records using custom builder logic', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-boolean-custom-builder';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::boolean('in_stock')
                    ->builder(function ($builder, $value) {
                        $builder->where('name', 'like', '%Peixada%');
                    }),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('in_stock');
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
        ->set('filters.boolean.in_stock', 'true')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Carne Louca');
});
