<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Enums\Diet;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Column,
    Filters\Filter,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent};

class DishesEnumTable extends PowerGridComponent
{
    use ActionButton;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Header::make()
                ->showToggleColumns()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Dish::with('category');
    }

    public function relationSearch(): array
    {
        return [
            'category' => [
                'name',
            ],
        ];
    }

    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('diet', function (Dish $dish) {
                return Diet::from($dish->diet)->labels();
            });
    }

    public function columns(): array
    {
        $canEdit = true;

        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Stored at', 'storage_room')
                ->sortable(),

            Column::make('Prato', 'name')
                ->searchable()
                ->editOnClick($canEdit)
                ->clickToCopy(true)
                ->placeholder('Prato placeholder')
                ->sortable(),

            Column::make('Dieta', 'diet', 'dishes.diet'),
        ];
    }

    public function actions(): array
    {
        return [];
    }

    public function filters(): array
    {
        return [
            Filter::enumSelect('diet', 'dishes.diet')
                ->dataSource(Diet::cases()),
        ];
    }

    public function bootstrap()
    {
        config(['livewire-powergrid.theme' => 'bootstrap']);
    }

    public function tailwind()
    {
        config(['livewire-powergrid.theme' => 'tailwind']);
    }
}
