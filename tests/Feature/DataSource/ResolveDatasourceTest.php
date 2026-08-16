<?php

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\DataSource\ProcessDataSource;

it('resolves the datasource and current table without running any query', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-resolve-datasource';

        public function datasource()
        {
            return Dish::query();
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

    $instance = Livewire::test($component::class)->instance();

    $queries = [];
    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });

    $processDataSource = ProcessDataSource::make($instance);
    $datasource = $processDataSource->resolveDatasource();

    expect($queries)->toBeEmpty()
        ->and($instance->currentTable)->toBe('dishes')
        ->and($datasource)->toBeInstanceOf(Builder::class);
});
