<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields};
use PowerComponents\Turbine\Column;

class DatasourceCollectionTable extends PowerGridComponent
{
    public string $tableName = 'testing-datasource-collection-table';

    public function datasource(): Collection
    {
        return collect([
            [
                'id' => 1,
                'name' => 'Name 1',
                'price' => 1.58,
                'chef_name' => '',
            ],
            [
                'id' => 2,
                'name' => 'Name 2',
                'price' => 1.68,
                'chef_name' => null,
            ],
            [
                'id' => 3,
                'name' => 'Name 3',
                'price' => 1.78,
                'chef_name' => 'Luan',
            ],
            [
                'id' => 4,
                'name' => 'Name 4',
                'price' => 1.88,
                'chef_name' => 'Luan',
            ],
            [
                'id' => 5,
                'name' => 'Name 5',
                'price' => 1.98,
                'chef_name' => 'Luan',
            ],
        ]);
    }

    public function setUp(): array
    {
        return [
            PowerGrid::detail()
                ->view('livewire-powergrid::tests.detail')
                ->showCollapseIcon(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('chef_name')
            ->add('price');
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
        ];
    }
}
