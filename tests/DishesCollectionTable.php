<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Support\{Collection};
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Column, PowerGrid, PowerGridComponent, PowerGridEloquent};

class DishesCollectionTable extends PowerGridComponent
{
    use ActionButton;

    public function datasource(): Collection
    {
        return collect([
            ['id' => 1, 'name' => 'Name 1', 'price' => 1.58, 'created_at' => '2021-01-01 00:00:00', ],
            ['id' => 2, 'name' => 'Name 2', 'price' => 1.68, 'created_at' => '2021-02-02 00:00:00', ],
            ['id' => 3, 'name' => 'Name 3', 'price' => 1.78, 'created_at' => '2021-03-03 00:00:00', ],
            ['id' => 4, 'name' => 'Name 4', 'price' => 1.88, 'created_at' => '2021-04-04 00:00:00', ],
            ['id' => 5, 'name' => 'Name 5', 'price' => 1.98, 'created_at' => '2021-05-05 00:00:00', ],
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
            ->addColumn('price')
            ->addColumn('created_at_formatted', function ($entry) {
                return \Illuminate\Support\Carbon::parse($entry->created_at)->format('d/m/Y');
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
                ->title(__('price'))
                ->field('price')
                ->sortable()
                ->makeInputRange('price', '.', ''),

            Column::add()
                ->title(__('Created At'))
                ->field('created_at_formatted')
                ->makeInputDatePicker('created_at'),
        ];
    }
}
