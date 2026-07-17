<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

uses()->group('datasource', 'builders', 'inputtext');

beforeEach(function () {
    TestDatabase::up();
});

it('filters database with input_text using contains operator (default)', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-contains';

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

it('filters database with input_text using is operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-is';

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
        ->set('filters.input_text_options.name.0', 'is')
        ->set('filters.input_text.name', 'Pastel de Nata')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
});

it('filters database with input_text using is_not operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-is-not';

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
        ->set('filters.input_text_options.name.0', 'is_not')
        ->set('filters.input_text.name', 'Pastel de Nata')
        ->assertDontSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia');
});

it('filters database with input_text using starts_with operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-starts';

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
        ->set('filters.input_text_options.name.0', 'starts_with')
        ->set('filters.input_text.name', 'Pastel')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
});

it('filters database with input_text using ends_with operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-ends';

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
        ->set('filters.input_text_options.name.0', 'ends_with')
        ->set('filters.input_text.name', 'Nata')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
});

it('filters database with input_text using contains_not operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-contains-not';

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
        ->set('filters.input_text_options.name.0', 'contains_not')
        ->set('filters.input_text.name', 'Pastel')
        ->assertDontSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia');
});

it('filters database with input_text using is_empty operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-empty';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::inputText('chef_name')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('chef_name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->set('filters.input_text_options.chef_name.0', 'is_empty')
        ->set('filters.input_text.chef_name', '')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
});

it('filters database with input_text using is_not_empty operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-not-empty';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::inputText('chef_name')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('chef_name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->set('filters.input_text_options.chef_name.0', 'is_not_empty')
        ->set('filters.input_text.chef_name', '')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Pastel de Nata');
});

it('filters database with input_text using is_null operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-null';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::inputText('chef_name')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('chef_name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->set('filters.input_text_options.chef_name.0', 'is_null')
        ->set('filters.input_text.chef_name', '')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
});

it('filters database with input_text using is_not_null operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-not-null';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::inputText('chef_name')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('chef_name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->set('filters.input_text_options.chef_name.0', 'is_not_null')
        ->set('filters.input_text.chef_name', '')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Pastel de Nata');
});

it('filters database with input_text using is_blank operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-blank';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::inputText('chef_name')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('chef_name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->set('filters.input_text_options.chef_name.0', 'is_blank')
        ->set('filters.input_text.chef_name', '')
        ->assertSee('Carne Louca')
        ->assertDontSee('Peixada da chef Nábia');
});

it('filters database with input_text using is_not_blank operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-not-blank';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [Filter::inputText('chef_name')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('chef_name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->set('filters.input_text_options.chef_name.0', 'is_not_blank')
        ->set('filters.input_text.chef_name', '')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Carne Louca');
});

it('filters database with custom builder logic', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-custom-builder';

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name')
                    ->builder(function ($builder, $values) {
                        $builder->where('in_stock', true);
                    }),
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
        ->set('filters.input_text.name', 'a')
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Barco-Sushi da Sueli'); // in_stock=false
});

it('filters collection with input_text using contains operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-collection-contains';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item One'],
                ['id' => 2, 'name' => 'Item Two'],
                ['id' => 3, 'name' => 'Another Item'],
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
        ->set('filters.input_text.name', 'One')
        ->assertSee('Item One')
        ->assertDontSee('Item Two');
});

it('filters collection with input_text using is operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-collection-is';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item One'],
                ['id' => 2, 'name' => 'Item Two'],
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
        ->set('filters.input_text_options.name.0', 'is')
        ->set('filters.input_text.name', 'Item One')
        ->assertSee('Item One')
        ->assertDontSee('Item Two');
});

it('filters collection with input_text using starts_with operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-collection-starts';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Apple Pie'],
                ['id' => 2, 'name' => 'Banana Bread'],
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
        ->set('filters.input_text_options.name.0', 'starts_with')
        ->set('filters.input_text.name', 'Apple')
        ->assertSee('Apple Pie')
        ->assertDontSee('Banana Bread');
});

it('filters collection with input_text using ends_with operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-collection-ends';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Apple Pie'],
                ['id' => 2, 'name' => 'Banana Bread'],
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
        ->set('filters.input_text_options.name.0', 'ends_with')
        ->set('filters.input_text.name', 'Pie')
        ->assertSee('Apple Pie')
        ->assertDontSee('Banana Bread');
});

it('filters collection with input_text using contains_not operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-collection-contains-not';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Apple Pie'],
                ['id' => 2, 'name' => 'Banana Bread'],
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
        ->set('filters.input_text_options.name.0', 'contains_not')
        ->set('filters.input_text.name', 'Apple')
        ->assertDontSee('Apple Pie')
        ->assertSee('Banana Bread');
});

it('filters collection with input_text using is_empty operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-collection-empty';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1', 'description' => ''],
                ['id' => 2, 'name' => 'Item 2', 'description' => 'Some text'],
                ['id' => 3, 'name' => 'Item 3', 'description' => null],
            ]);
        }

        public function filters(): array
        {
            return [Filter::inputText('description')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('description');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->set('filters.input_text_options.description.0', 'is_empty')
        ->set('filters.input_text.description', '')
        ->assertSee('Item 1')
        ->assertSee('Item 3')
        ->assertDontSee('Item 2');
});

it('filters collection with input_text using is_not_empty operator', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-collection-not-empty';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1', 'description' => ''],
                ['id' => 2, 'name' => 'Item 2', 'description' => 'Some text'],
            ]);
        }

        public function filters(): array
        {
            return [Filter::inputText('description')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('description');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->set('filters.input_text_options.description.0', 'is_not_empty')
        ->set('filters.input_text.description', '')
        ->assertSee('Item 2')
        ->assertDontSee('Item 1');
});

it('filters collection with custom collection logic', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-inputtext-collection-custom';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item 1'],
                ['id' => 2, 'name' => 'Item 2'],
                ['id' => 3, 'name' => 'Item 3'],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name')
                    ->collection(function ($collection, $values) {
                        return $collection->where('id', '>', 1);
                    }),
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
        ->set('filters.input_text.name', 'Item')
        ->assertDontSee('Item 1')
        ->assertSee('Item 2')
        ->assertSee('Item 3');
});

function collectionNullabilityComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'collection-nullability') {}

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Item Empty', 'description' => ''],
                ['id' => 2, 'name' => 'Item Text', 'description' => 'Some text'],
                ['id' => 3, 'name' => 'Item Null', 'description' => null],
            ]);
        }

        public function filters(): array
        {
            return [Filter::inputText('description')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('description');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };
}

it('filters collection with input_text using is_null operator', function () {
    Livewire::test(collectionNullabilityComponent('collection-is-null')::class)
        ->set('filters.input_text_options.description.0', 'is_null')
        ->set('filters.input_text.description', '')
        ->assertSee('Item Null')
        ->assertDontSee('Item Empty')
        ->assertDontSee('Item Text');
});

it('filters collection with input_text using is_not_null operator', function () {
    // is_not_null on a collection means: not null AND not empty string
    Livewire::test(collectionNullabilityComponent('collection-is-not-null')::class)
        ->set('filters.input_text_options.description.0', 'is_not_null')
        ->set('filters.input_text.description', '')
        ->assertSee('Item Text')
        ->assertDontSee('Item Empty')
        ->assertDontSee('Item Null');
});

it('filters collection with input_text using is_blank operator', function () {
    // is_blank on a collection means: not null AND empty string
    Livewire::test(collectionNullabilityComponent('collection-is-blank')::class)
        ->set('filters.input_text_options.description.0', 'is_blank')
        ->set('filters.input_text.description', '')
        ->assertSee('Item Empty')
        ->assertDontSee('Item Text')
        ->assertDontSee('Item Null');
});

it('filters collection with input_text using is_not_blank operator', function () {
    // is_not_blank on a collection means: not empty string OR null
    Livewire::test(collectionNullabilityComponent('collection-is-not-blank')::class)
        ->set('filters.input_text_options.description.0', 'is_not_blank')
        ->set('filters.input_text.description', '')
        ->assertSee('Item Text')
        ->assertSee('Item Null')
        ->assertDontSee('Item Empty');
});
