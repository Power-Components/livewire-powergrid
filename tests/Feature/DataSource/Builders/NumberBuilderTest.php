<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

uses()->group('datasource', 'builders', 'number');

beforeEach(function () {
    TestDatabase::up();
});

it('filters database records with number filter start and end', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-range';

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
        ->set('filters.number.price', ['start' => 15, 'end' => 35])
        ->assertSee('Peixada da chef Nábia') // 20.50
        ->assertSee('Carne Louca') // 30.00
        ->assertDontSee('Pastel de Nata') // 10.00
        ->assertDontSee('Bife à Rolê'); // 40.50
});

it('filters database records with number filter only start', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-start';

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
        ->set('filters.number.price', ['start' => 40])
        ->assertSee('Bife à Rolê') // 40.50
        ->assertSee('Francesinha') // 60.50
        ->assertDontSee('Pastel de Nata') // 10.00
        ->assertDontSee('Carne Louca'); // 30.00
});

it('filters database records with number filter only end', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-end';

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
        ->set('filters.number.price', ['end' => 25])
        ->assertSee('Pastel de Nata') // 10.00
        ->assertSee('Peixada da chef Nábia') // 20.50
        ->assertDontSee('Carne Louca') // 30.00
        ->assertDontSee('Bife à Rolê'); // 40.50
});

it('filters database records with number filter and custom builder', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-custom';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::number('price')
                    ->builder(function ($builder, $values) {
                        $builder->where('in_stock', true);
                    }),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price')->add('in_stock');
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
        ->set('filters.number.price', ['start' => 10, 'end' => 50])
        ->assertSee('Pastel de Nata') // in_stock=true, price=10
        ->assertSee('Peixada da chef Nábia') // in_stock=true, price=20.50
        ->assertSee('Carne Louca') // in_stock=true, price=30
        ->assertDontSee('Barco-Sushi da Sueli'); // price=5000 (out of range)
});

it('filters collection records with number filter start and end', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-collection-range';

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
        ->assertSee('Item 2')
        ->assertSee('Item 3')
        ->assertDontSee('Item 1')
        ->assertDontSee('Item 4');
});

it('filters collection records with number filter only start', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-collection-start';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1', 'price' => 10],
                ['id' => 2, 'name' => 'Item 2', 'price' => 20],
                ['id' => 3, 'name' => 'Item 3', 'price' => 30],
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
        ->set('filters.number.price', ['start' => 20])
        ->assertSee('Item 2')
        ->assertSee('Item 3')
        ->assertDontSee('Item 1');
});

it('filters collection records with number filter only end', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-collection-end';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1', 'price' => 10],
                ['id' => 2, 'name' => 'Item 2', 'price' => 20],
                ['id' => 3, 'name' => 'Item 3', 'price' => 30],
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
        ->set('filters.number.price', ['end' => 20])
        ->assertSee('Item 1')
        ->assertSee('Item 2')
        ->assertDontSee('Item 3');
});

it('filters collection with custom collection logic', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-collection-custom';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1', 'price' => 10],
                ['id' => 2, 'name' => 'Item 2', 'price' => 20],
                ['id' => 3, 'name' => 'Item 3', 'price' => 30],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::number('price')
                    ->collection(function ($collection, $values) {
                        return $collection->where('id', '>', 1);
                    }),
            ];
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
        ->assertDontSee('Item 1')
        ->assertSee('Item 2')
        ->assertSee('Item 3');
});

it('handles number filter with thousands separator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-thousands';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::number('price')
                    ->thousands(','),
            ];
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
        ->set('filters.number.price', ['start' => '1,000', 'end' => '10,000'])
        ->assertSee('Barco-Sushi da Sueli'); // 5000.00
});

it('handles number filter with decimal separator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-decimal';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::number('price')
                    ->decimal(','),
            ];
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
        ->set('filters.number.price', ['start' => '20,50', 'end' => '30,00'])
        ->assertSee('Peixada da chef Nábia') // 20.50
        ->assertSee('Carne Louca') // 30.00
        ->assertDontSee('Pastel de Nata'); // 10.00
});

it('returns all records when number filter values are not set', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-builder-empty';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1', 'price' => 10],
                ['id' => 2, 'name' => 'Item 2', 'price' => 20],
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
        ->assertSee('Item 1')
        ->assertSee('Item 2');
});
