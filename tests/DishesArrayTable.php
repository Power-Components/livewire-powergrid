<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Support\{Carbon};
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{
    Button,
    Column,
    Exportable,
    Footer,
    Header,
    PowerGrid,
    PowerGridColumns,
    PowerGridComponent
};

class DishesArrayTable extends PowerGridComponent
{
    use ActionButton;

    public array $eventId = [];

    public array $testFilters = [];

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

    public function datasource(): array
    {
        return [
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
        ];
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            Header::make()
                ->showToggleColumns()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
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
                ->sortable(),

            Column::add()
                ->title(__('Chef'))
                ->field('chef_name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title(__('Price'))
                ->field('price')
                ->sortable(),

            Column::add()
                ->title(__('In Stock'))
                ->toggleable(true, 'sim', 'não')
                ->field('in_stock'),

            Column::add()
                ->title(__('Created At'))
                ->field('created_at_formatted'),
        ];
    }

    public function actions(): array
    {
        return [
            Button::add('edit-stock')
                ->caption('<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => 'id']),
        ];
    }

    public function filters(): array
    {
        return $this->testFilters;
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
