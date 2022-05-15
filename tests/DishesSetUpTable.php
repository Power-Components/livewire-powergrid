<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    Detail,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent,
    Rules\Rule};

class DishesSetUpTable extends PowerGridComponent
{
    use ActionButton;

    public bool $join = false;

    public bool $testHeader = false;

    public bool $testFooter = false;

    public function setUp(): array
    {
        $this->showCheckBox();

        if ($this->testHeader) {
            return [
                Header::make()
                    ->showSearchInput()
                    ->includeViewOnTop('livewire-powergrid::tests.header-top')
                    ->includeViewOnBottom('livewire-powergrid::tests.header-bottom'),

            ];
        }

        if ($this->testFooter) {
            return [
                Footer::make()
                    ->includeViewOnTop('livewire-powergrid::tests.footer-top')
                    ->includeViewOnBottom('livewire-powergrid::tests.footer-bottom'),

            ];
        }
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

    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('name');
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title(__('ID'))
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title(__('Prato'))
                ->field('name')
                ->searchable()
                ->sortable(),
        ];
    }

    public function actions(): array
    {
        return [
            Button::make('toggleDetail', 'Toggle Detail')
                ->class('text-center')
                ->toggleDetail(),
        ];
    }
}
