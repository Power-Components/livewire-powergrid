<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields, TurbineTable};
use PowerComponents\Turbine\{Column, Fields, GridDefinition, Turbine};
use PowerComponents\Turbine\Contracts\GridSchema;

class SharedDishesGrid extends GridDefinition
{
    public function datasource(): mixed
    {
        return collect([
            ['id' => 1, 'name' => 'Pastel'],
            ['id' => 2, 'name' => 'Francesinha'],
            ['id' => 3, 'name' => 'Peixada'],
        ]);
    }

    public function fields(): Fields
    {
        return Fields::make()->add('id')->add('name');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name')->searchable(),
        ];
    }

    public function setUp(): array
    {
        return [
            Turbine::footer()->showPerPage(25, [25, 50]),
        ];
    }
}

it('drives a Livewire grid from a shared GridDefinition', function () {
    $component = new class() extends TurbineTable
    {
        public string $tableName = 'shared-dishes';

        protected function definition(): GridSchema
        {
            return new SharedDishesGrid();
        }
    };

    Livewire::test($component::class)
        ->assertSee('Pastel')
        ->assertSee('Francesinha')
        ->set('search', 'Peixada')
        ->assertSee('Peixada')
        ->assertDontSee('Francesinha');
});

it('resolves columns, fields and setUp through the definition', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'shared-dishes-columns';

        protected function definition(): GridSchema
        {
            return new SharedDishesGrid();
        }
    };

    expect($component->columns())->toHaveCount(2)
        ->and($component->fields())->toBeInstanceOf(PowerGridFields::class)
        ->and($component->fields()->fields)->toHaveKeys(['id', 'name'])
        ->and($component->setUp())->toHaveKey('footer');
});

it('keeps the native behaviour when no definition is set', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'native-dishes';

        public function datasource(): mixed
        {
            return collect([['id' => 1, 'name' => 'Coxinha']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->searchable(),
            ];
        }
    };

    Livewire::test($component::class)->assertSee('Coxinha');

    expect($component->setUp())->toBe([]);
});
