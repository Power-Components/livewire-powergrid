<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Enums\Diet;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\{
    Column,
    Facades\Filter,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridFields
};

class DishesEnumTable extends PowerGridComponent
{
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Header::make()
                ->showToggleColumns()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
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

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('diet', function (Dish $dish) {
                return Diet::from($dish->diet)->labels();
            });
    }

    public function columns(): array
    {
        $canEdit = true;

        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Stored at', 'storage_room')
                ->sortable(),

            Column::make('Prato', 'name')
                ->searchable()
                ->placeholder('Prato placeholder')
                ->sortable(),

            Column::make('Dieta', 'diet', 'dishes.diet'),

            Column::action('Action'),
        ];
    }

    public function actions(): array
    {
        return [];
    }

    public function filters(): array
    {
        return [
            Filter::enumSelect('diet', 'dishes.diet')
                ->dataSource(Diet::cases()),
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
