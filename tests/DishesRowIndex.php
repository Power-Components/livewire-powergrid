<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{
    Button,
    Column,
    Filters\Filter,
    Footer,
    Header,
    PowerGrid,
    PowerGridColumns,
    PowerGridComponent
};

class DishesRowIndex extends PowerGridComponent
{
    use ActionButton;

    public bool $join = false;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Header::make()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
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
        return Dish::with('category');
    }

    public function join(): Builder
    {
        return Dish::query()
            ->join('categories', function ($categories) {
                $categories->on('dishes.category_id', '=', 'categories.id');
            })
            ->select('dishes.*', 'categories.name as category_name');
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('name');
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name', 'name')->operators(),
        ];
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->index(),

            Column::add()
                ->title(__('Name'))
                ->field('name')
                ->searchable()
                ->sortable(),
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
