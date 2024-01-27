<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Column, Header, PowerGrid, PowerGridComponent, PowerGridFields};

class NestedRelationSearchTable extends PowerGridComponent
{
    public bool $join = false;

    public function setUp(): array
    {
        return [
            Header::make()
                ->showSearchInput(),
        ];
    }

    public function datasource(): Builder
    {
        if ($this->join) {
            return $this->join();
        }

        return $this->query();
    }

    public function query(): Builder
    {
        return Dish::query()
            ->join('chefs', function ($chefs) {
                $chefs->on('dishes.chef_id', '=', 'chefs.id');
            })
            ->join('restaurants', function ($restaurants) {
                $restaurants->on('chefs.restaurant_id', '=', 'restaurants.id');
            })
            ->select('dishes.*', 'chefs.name as chef_name', 'restaurants.name as restaurant_name');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('chef_name')
            ->add('restaurant_name')
            ->add('in_stock');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Dish', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Chef', 'chef_name')
                ->searchable()
                ->sortable(),

            Column::make('Restaurant', 'restaurant_name')
                ->searchable()
                ->sortable(),
        ];
    }

    public function relationSearch(): array
    {
        return [
            'chef' => ['name', 'restaurants' => ['name']],
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
