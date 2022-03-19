<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use NumberFormatter;
use PowerComponents\LivewirePowerGrid\Tests\Enums\Diet;
use PowerComponents\LivewirePowerGrid\Tests\Models\{Category, Dish};
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent,
    Rules\Rule};

class DishesEnumTable extends PowerGridComponent
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
        return Dish::with('category');
    }

    public function relationSearch(): array
    {
        return [
            'category' => [
                'name',
            ],
        ];
    }

    public function addColumns(): ?PowerGridEloquent
    {
        $fmt = new NumberFormatter('ca_ES', NumberFormatter::CURRENCY);

        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('diet', function (Dish $dish) {
                return Diet::from($dish->diet)->labels();
            });
    }

    public function columns(): array
    {
        $canEdit = true;

        return [
            Column::add()
                ->title(__('ID'))
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title(__('Stored at'))
                ->field('storage_room')
                ->sortable(),

            Column::add()
                ->title(__('Prato'))
                ->field('name')
                ->searchable()
                ->editOnClick($canEdit)
                ->clickToCopy(true)
                ->makeInputText('name')
                ->placeholder('Prato placeholder')
                ->sortable(),
                
            Column::add()
                ->field('diet', 'dishes.diet')
                ->makeInputEnumSelect(Diet::cases(), 'dishes.diet')
                ->title(__('Dieta')),
        ];
    }

    public function actions(): array
    {
        return [];
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
