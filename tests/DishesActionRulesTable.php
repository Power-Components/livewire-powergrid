<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    Detail,
    Exportable,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent,
    Rules\Rule};

class DishesActionRulesTable extends PowerGridComponent
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

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Header::make()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),

            Detail::make()
                ->view('livewire-powergrid::tests.detail-rules')
                ->options([
                    'name' => 'Luan Freitas',
                ])
                ->showCollapseIcon(),
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
                ->makeInputText('name')
                ->sortable(),
        ];
    }

    public function actions(): array
    {
        return [
            Button::add('edit-stock-for-rules')
                ->caption('<div id="edit">Edit for Rules</div>')
                ->class('text-center')
                ->openModal('edit-stock-for-rules', ['dishId' => 'id']),
        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::button('edit-stock-for-rules')
                ->when(fn (Dish $dish) => $dish->id == 2)
                ->hide(),

            Rule::button('edit-stock-for-rules')
                ->when(fn (Dish $dish) => $dish->id == 4)
                ->caption('cation edit for id 4'),

            Rule::button('edit-stock-for-rules')
                ->when(fn (Dish $dish)     => (bool) $dish->in_stock === false && $dish->id !== 8 && $dish->id !== 5)
                ->redirect(fn (Dish $dish) => 'https://www.dish.test/sorry-out-of-stock?dish=' . $dish->id),

            // Set a row red background for when dish is out of stock
            Rule::rows()
                ->when(fn (Dish $dish) => (bool) $dish->in_stock === false)
                ->setAttribute('class', 'bg-red-100 text-red-800'),

            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id == 3)
                ->setAttribute('class', 'bg-blue-100'),

            Rule::button('edit-stock-for-rules')
                ->when(fn (Dish $dish) => $dish->id == 5)
                ->emit('toggleEvent', ['dishId' => 'id']),

            Rule::button('edit-stock-for-rules')
                ->when(fn (Dish $dish) => $dish->id == 9)
                ->disable(),
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
