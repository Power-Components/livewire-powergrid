<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Support\{Carbon, Collection};
use PowerComponents\LivewirePowerGrid\{
    Button,
    Column,
    Exportable,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridFields
};

class DishesCollectionTable extends PowerGridComponent
{
    public array $testFilters = [];

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

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('chef_name')
            ->add('price')
            ->add('in_stock')
            ->add('in_stock_label', function ($entry) {
                return ($entry->in_stock ? 'sim' : 'não');
            })
            ->add('created_at_formatted', function ($entry) {
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
                ->toggleable(true, 'yes', 'no')
                ->field('in_stock'),

            Column::add()
                ->title(__('Created At'))
                ->field('created_at_formatted'),

            Column::action('Action'),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::add('edit-stock')
                ->slot('<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => $row['id']]),

            Button::add('edit-stock-for-rules')
                ->slot('<div id="edit">Edit for Rules</div>')
                ->class('text-center')
                ->openModal('edit-stock-for-rules', ['dishId' => $row['id']]),

            Button::add('destroy')
                ->slot(__('Delete'))
                ->class('text-center')
                ->dispatch('deletedEvent', ['dishId' => $row['id']])
                ->method('delete'),
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
