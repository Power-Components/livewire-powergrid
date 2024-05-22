<?php

use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTable;
use PowerComponents\LivewirePowerGrid\{Button, Column, Detail};

$baseRuleComponent = new class () extends DishesTable {
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            ...parent::setUp(),

            Detail::make()
                ->view('components.detail')
                ->showCollapseIcon()
                ->params(['name' => 'Luan']),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('id', 'id'),

            Column::add()
                ->title('In Stock')
                ->field('in_stock')
                ->toggleable(hasPermission: true, trueLabel: 'it-is-in-stock', falseLabel: 'it-is-not-in-stock')
                ->sortable(),

            Column::make('NEVER HAS TOGGLEABLE', 'active')
                ->toggleable(hasPermission: false, trueLabel: 'it-is-active', falseLabel: 'it-is-not-active')
                ->sortable(),

            Column::make('Name', 'name')
                ->searchable()
                ->editOnClick(hasPermission: true, dataField: 'name')
                ->sortable(),

            Column::make('NEVER HAS EDIT ON CLICK', 'serving_at')
                ->searchable()
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::make('dispatch')
                ->slot('dispatch: ' . $row->id)
                ->dispatch('executeDispatch', ['id' => $row->id]),
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::rows()
                ->when(fn ($dish) => $dish->id == 1)
                ->hideToggleDetail(),

            Rule::toggleable('active')
                ->when(fn ($dish) => $dish->id == 1)
                ->show(),

            Rule::editOnClick('serving_at')
                ->when(fn ($dish) => $dish->id == 1)
                ->enable(),

            Rule::rows()
                ->when(fn ($dish) => $dish->id == 5)
                ->hideToggleable()
                ->disableEditOnClick(),
        ];
    }
};
