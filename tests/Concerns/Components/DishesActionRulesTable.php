<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\{
    Button,
    Column,
    Facades\Rule,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridFields
};

class DishesActionRulesTable extends PowerGridComponent
{
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
            Button::add('edit-stock-for-rules')
                ->slot('<div id="edit">Edit for Rules</div>')
                ->class('text-center')
                ->openModal('edit-stock-for-rules', ['dishId' => 'id']),
        ];
    }

    public function actionRules(): array
    {
        return [
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
