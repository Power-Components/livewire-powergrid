<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    Exportable,
    Footer,
    Header,
    PowerGrid,
    PowerGridColumns,
    PowerGridComponent};

class DishesSoftDeletesTable extends PowerGridComponent
{
    public function setUp(): array
    {
        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            Header::make()
                ->showSoftDeletes()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Dish::query();
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('deleted_at');
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
                ->placeholder('Prato placeholder')
                ->sortable(),

            Column::add()
                ->title(__('Data'))
                ->field('deleted_at')
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function actions(Dish $dish): array
    {
        return [
            Button::add('edit-stock')
                ->slot('<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => $dish->id]),

            Button::add('destroy')
                ->slot(__('Delete'))
                ->class('text-center')
                ->dispatch('deletedEvent', ['dishId' => $dish->id])
                ->method('delete'),
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
