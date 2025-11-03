<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Carbon\Carbon;
use PowerComponents\LivewirePowerGrid\{Button, Column, Facades\PowerGrid, PowerGridFields};

class DishesIterableTable extends BaseDishesTable
{
    public string $tableName = 'testing-dishes-iterable-table';

    public string $iterableType = 'array';

    public function datasource()
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Name 1',
                'price' => 1.58,
                'in_stock' => true,
                'created_at' => '2021-01-01 00:00:00',
                'chef_name' => '',
            ],
            [
                'id' => 2,
                'name' => 'Name 2',
                'price' => 1.68,
                'in_stock' => true,
                'created_at' => '2021-02-02 00:00:00',
                'chef_name' => null,
            ],
            [
                'id' => 3,
                'name' => 'Name 3',
                'price' => 1.78,
                'in_stock' => false,
                'created_at' => '2021-03-03 00:00:00',
                'chef_name' => 'Luan',
            ],
            [
                'id' => 4,
                'name' => 'Name 4',
                'price' => 1.88,
                'in_stock' => true,
                'created_at' => '2021-04-04 00:00:00',
                'chef_name' => 'Luan',
            ],
            [
                'id' => 5,
                'name' => 'Name 5',
                'price' => 1.98,
                'in_stock' => false,
                'created_at' => '2021-05-05 00:00:00',
                'chef_name' => 'Luan',
            ],
        ];

        if ($this->iterableType === 'collection') {
            return collect($data);
        }

        return $data;
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
                return $entry->in_stock ? 'yes' : 'no';
            })
            ->add('created_at_formatted', function ($entry) {
                return Carbon::parse($entry->created_at)->format('d/m/Y');
            });
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title('ID')
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Name')
                ->field('name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Chef')
                ->field('chef_name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Price')
                ->field('price')
                ->sortable(),

            Column::add()
                ->title('In Stock')
                ->toggleable(true, 'sim', 'não')
                ->field('in_stock'),

            Column::add()
                ->title('Created At')
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
                ->openModal('edit-stock', ['dishId' => 'id']),
        ];
    }
}
