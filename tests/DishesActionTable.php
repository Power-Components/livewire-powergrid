<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent};

class DishesActionTable extends PowerGridComponent
{
    use ActionButton;

    public array $eventId = [];

    public bool $join = false;

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'deletedEvent',
            ]
        );
    }

    public function openModal(array $params)
    {
        $this->eventId = $params;
    }

    public function deletedEvent(array $params)
    {
        $this->eventId = $params;
    }

    public function setUp()
    {
        $this->showCheckBox()
            ->showPerPage()
            ->showRecordCount()
            ->showToggleColumns()
            ->showExportOption('download-test', ['excel', 'csv'])
            ->showSearchInput();
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

    public function addColumns(): ?PowerGridEloquent
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
                ->makeInputText('name')
                ->sortable(),
        ];
    }

    public function actions(): array
    {
        return [
            Button::add('openModal')
                ->caption('openModal')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => 'id']),

            Button::add('emit')
                ->caption(__('delete'))
                ->class('text-center')
                ->emit('deletedEvent', ['dishId' => 'id']),

            Button::add('emitTo')
                ->caption(__('emit'))
                ->class('text-center')
                ->emitTo('dishes-table', 'deletedEvent', ['dishId' => 'id']),
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
