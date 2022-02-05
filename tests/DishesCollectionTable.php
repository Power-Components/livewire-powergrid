<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Support\{Carbon, Collection};
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent,
    Rules\Rule};

class DishesCollectionTable extends PowerGridComponent
{
    use ActionButton;

    public array $eventId = [];

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

    public function datasource(): Collection
    {
        return collect([
            [
                'id'         => 1,
                'name'       => 'Name 1',
                'price'      => 1.58,
                'in_stock'   => true,
                'created_at' => '2021-01-01 00:00:00',
                'chef_name'  => '',
            ],
            [
                'id'         => 2,
                'name'       => 'Name 2',
                'price'      => 1.68,
                'in_stock'   => true,
                'created_at' => '2021-02-02 00:00:00',
                'chef_name'  => null,
            ],
            [
                'id'         => 3,
                'name'       => 'Name 3',
                'price'      => 1.78,
                'in_stock'   => false,
                'created_at' => '2021-03-03 00:00:00',
                'chef_name'  => 'Luan',
            ],
            [
                'id'         => 4,
                'name'       => 'Name 4',
                'price'      => 1.88,
                'in_stock'   => true,
                'created_at' => '2021-04-04 00:00:00',
                'chef_name'  => 'Luan',
            ],
            [
                'id'         => 5,
                'name'       => 'Name 5',
                'price'      => 1.98,
                'in_stock'   => false,
                'created_at' => '2021-05-05 00:00:00',
                'chef_name'  => 'Luan',
            ],
        ]);
    }

    public function setUp()
    {
        $this->showCheckBox()
            ->showPerPage()
            ->showExportOption('download', ['excel', 'csv'])
            ->showSearchInput();
    }

    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('chef_name')
            ->addColumn('price')
            ->addColumn('in_stock')
            ->addColumn('in_stock_label', function ($entry) {
                return ($entry->in_stock ? 'sim' : 'não');
            })
            ->addColumn('created_at_formatted', function ($entry) {
                return Carbon::parse($entry->created_at)->format('d/m/Y');
            });
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
                ->title(__('Name'))
                ->field('name')
                ->searchable()
                ->makeInputText('name')
                ->sortable(),

            Column::add()
                ->title(__('Chef'))
                ->field('chef_name')
                ->searchable()
                ->makeInputText('chef_name')
                ->sortable(),

            Column::add()
                ->title(__('Price'))
                ->field('price')
                ->sortable()
                ->makeInputRange('price', '.', ''),

            Column::add()
                ->title(__('In Stock'))
                ->toggleable(true, 'sim', 'não')
                ->makeBooleanFilter('in_stock', 'sim', 'não')
                ->field('in_stock'),

            Column::add()
                ->title(__('Created At'))
                ->field('created_at_formatted')
                ->makeInputDatePicker('created_at'),
        ];
    }

    public function actions(): array
    {
        return [
            Button::add('edit-stock')
                ->caption('<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => 'id']),

            Button::add('edit-stock-for-rules')
                ->caption('<div id="edit">Edit for Rules</div>')
                ->class('text-center')
                ->openModal('edit-stock-for-rules', ['dishId' => 'id']),

            Button::add('destroy')
                ->caption(__('Delete'))
                ->class('text-center')
                ->emit('deletedEvent', ['dishId' => 'id'])
                ->method('delete'),
        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 2)
                ->hide(),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 4)
                ->caption('cation edit for id 4'),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish)     => (bool) $dish->in_stock === false && $dish->id !== 8)
                ->redirect(fn ($dish) => 'https://www.dish.test/sorry-out-of-stock?dish=' . $dish->id),

            // Set a row red background for when dish is out of stock
            Rule::rows()
                ->when(fn ($dish) => (bool) $dish->in_stock === false)
                ->setAttribute('class', 'bg-red-100 text-red-800'),

            Rule::rows()
                ->when(fn ($dish) => $dish->id == 3)
                ->setAttribute('class', 'bg-blue-100'),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 5)
                ->emit('toggleEvent', ['dishId' => 'id']),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 9)
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
