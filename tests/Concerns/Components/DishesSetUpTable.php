<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Button, Column, Footer, Header, PowerGrid, PowerGridComponent, PowerGridFields};

class DishesSetUpTable extends PowerGridComponent
{
    public bool $join = false;

    public bool $testHeader = false;

    public bool $testFooter = false;

    public array $testCache = [];

    public function setUp(): array
    {
        $this->showCheckBox();

        if ($this->testCache) {
            return $this->testCache;
        }

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

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name');
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

            Column::action('Action'),
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
