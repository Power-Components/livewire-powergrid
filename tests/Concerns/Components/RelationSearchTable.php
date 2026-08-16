<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\Column;

class RelationSearchTable extends PowerGridComponent
{
    public string $tableName = 'testing-nested-relation-search-table';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput(),
        ];
    }

    public function datasource(): Builder
    {
        return $this->query();
    }

    public function query(): Builder
    {
        return Dish::query()->with('category');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('chef_name', fn ($dish) => e($dish->chef?->name))
            ->add('category_name', fn ($dish) => e($dish->category->name));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Dish', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Category', 'category_name')
                ->searchable(),

            Column::make('Chef', 'chef_name')
                ->searchable(),
        ];
    }

    public function relationSearch(): array
    {
        return [
            'category' => [
                'name',
            ],

            'chef' => [
                'name',
            ],
        ];
    }

    public function setTestThemeClass(string $themeClass): void
    {
        config(['livewire-powergrid.theme' => $themeClass]);
    }
}
