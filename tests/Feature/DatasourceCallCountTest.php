<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

class DatasourceCountTable extends PowerGridComponent
{
    public string $tableName = 'datasource-count-table';

    public static int $calls = 0;

    public function datasource(): Collection
    {
        self::$calls++;

        return collect([
            ['id' => 1, 'name' => 'Name 1'],
            ['id' => 2, 'name' => 'Name 2'],
        ]);
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
            Column::make('ID', 'id'),
            Column::make('Name', 'name'),
        ];
    }
}

class EloquentDatasourceCountTable extends PowerGridComponent
{
    public string $tableName = 'eloquent-datasource-count-table';

    public static int $calls = 0;

    public function datasource(): Builder
    {
        self::$calls++;

        return Dish::query();
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
            Column::make('ID', 'id'),
            Column::make('Name', 'name'),
        ];
    }
}

it('calls collection datasource() only once', function () {
    DatasourceCountTable::$calls = 0;

    livewire(DatasourceCountTable::class)
        ->assertOk();

    expect(DatasourceCountTable::$calls)->toBe(1);
});

it('calls collection datasource() only once when searching', function () {
    DatasourceCountTable::$calls = 0;

    livewire(DatasourceCountTable::class)
        ->set('search', 'Name 1')
        ->assertOk();

    expect(DatasourceCountTable::$calls)->toBe(2);
});

it('calls eloquent datasource() only once', function () {
    EloquentDatasourceCountTable::$calls = 0;

    livewire(EloquentDatasourceCountTable::class)
        ->assertOk();

    expect(EloquentDatasourceCountTable::$calls)->toBe(1);
});

it('calls eloquent datasource() only once when searching', function () {
    EloquentDatasourceCountTable::$calls = 0;

    livewire(EloquentDatasourceCountTable::class)
        ->set('search', 'Dish 1')
        ->assertOk();

    expect(EloquentDatasourceCountTable::$calls)->toBe(2);
});
