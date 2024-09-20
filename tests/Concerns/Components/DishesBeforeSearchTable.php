<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

class DishesBeforeSearchTable extends PowerGridComponent
{
    public string $tableName = 'testing-dishes-before-search-table';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function beforeSearchName(string $search): string
    {
        return 'Peixada';
    }

    public function beforeSearch(?string $field = null, ?string $search = null): ?string
    {
        if ($field === 'in_stock') {
            return str($search)
                ->replace('without_stock', DB::getDriverName() === 'pgsql' ? 'false' : '0')
                ->replace('with_stock', DB::getDriverName() === 'pgsql' ? 'true' : '1')
                ->toString();
        }

        return $search;
    }

    public function datasource(): Builder
    {
        return Dish::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('in_stock')
            ->add('name');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Dish', 'name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Stock', 'in_stock', 'in_stock')
                ->searchable(),

            Column::action('Action'),
        ];
    }

    public function setTestThemeClass(string $themeClass): void
    {
        config(['livewire-powergrid.theme' => $themeClass]);
    }
}
