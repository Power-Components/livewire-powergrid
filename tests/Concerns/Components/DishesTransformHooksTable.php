<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\{Button, Column};

class DishesTransformHooksTable extends PowerGridComponent
{
    public string $tableName = 'testing-dishes-transform-hooks-table';

    public bool $useTransformRows = false;

    public bool $useTransformQuery = false;

    public bool $useTransformActions = false;

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Dish::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('price')
            ->add('calories')
            ->add('in_stock')
            ->add('produced_at')
            ->add('custom_label');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Dish', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Price', 'price')
                ->sortable(),

            Column::make('Calories', 'calories')
                ->sortable(),

            Column::make('In Stock', 'in_stock'),

            Column::make('Custom Label', 'custom_label'),

            Column::action('Action'),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class('btn-edit')
                ->dispatch('editDish', ['id' => $row->id]),
        ];
    }

    public function transformRows(Collection $rows): Collection
    {
        if (! $this->useTransformRows) {
            return $rows;
        }

        return $rows->map(function ($row) {
            $row->custom_label = 'custom-'.$row->id;
            $row->calories = $row->calories * 2;

            return $row;
        });
    }

    public function transformQuery(mixed $query): mixed
    {
        if (! $this->useTransformQuery) {
            return $query;
        }

        return $query->where('in_stock', true);
    }

    public function transformActions(array $actionsByRow, Collection $rows): array
    {
        if (! $this->useTransformActions) {
            return $actionsByRow;
        }

        foreach ($actionsByRow as $rowId => &$actions) {
            foreach ($actions as &$action) {
                if ($action['action'] === 'edit') {
                    $action['slot'] = 'Edit #'.$rowId;
                }
            }
        }

        return $actionsByRow;
    }

    public function setTestThemeClass(string $themeClass): void
    {
        config(['livewire-powergrid.theme' => $themeClass]);
    }
}
