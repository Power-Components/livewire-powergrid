<?php

use Illuminate\Support\Collection;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

class TestTransformRowsTable extends PowerGridComponent
{
    public string $tableName = 'test-transform-rows';

    public bool $useTransformRows = false;

    public function datasource()
    {
        return collect([
            ['id' => 1, 'name' => 'Dish 1'],
            ['id' => 2, 'name' => 'Dish 2'],
        ]);
    }

    public function transformRows(Collection $rows): Collection
    {
        if (! $this->useTransformRows) {
            return $rows;
        }

        return $rows->map(function ($row) {
            data_set($row, 'custom_label', 'custom-'.data_get($row, 'id'));

            return $row;
        });
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('custom_label');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Custom', 'custom_label'),
        ];
    }
}

class TestTransformQueryTable extends PowerGridComponent
{
    public string $tableName = 'test-transform-query';

    public bool $useTransformQuery = false;

    public function datasource()
    {
        return collect([
            ['id' => 1, 'name' => 'Dish 1', 'active' => true],
            ['id' => 2, 'name' => 'Dish 2', 'active' => false],
        ]);
    }

    public function transformQuery(mixed $query): mixed
    {
        if (! $this->useTransformQuery) {
            return $query;
        }

        return $query->filter(fn ($row) => data_get($row, 'active') === true);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name'),
        ];
    }
}

it('transformRows modifies row data before rendering', function () {
    Livewire::test(TestTransformRowsTable::class)
        ->assertDontSee('custom-1')
        ->set('useTransformRows', true)
        ->assertSee('custom-1')
        ->assertSee('custom-2');
});

it('transformQuery filters the data before pagination', function () {
    Livewire::test(TestTransformQueryTable::class)
        ->assertSee('Dish 1')
        ->assertSee('Dish 2')
        ->set('useTransformQuery', true)
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2');
});
