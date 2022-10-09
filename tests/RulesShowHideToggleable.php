<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\{
    Column,
    Footer,
    Header,
    PowerGrid,
    PowerGridEloquent};

class RulesShowHideToggleable extends DishesTable
{
    public array $testActionRules = [];

    public function setUp(): array
    {
        return [
            Header::make()
                ->showToggleColumns()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),

        ];
    }

    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent()
                  ->addColumn('id')
                  ->addColumn('in_stock')
                  ->addColumn('in_stock_label', function (Dish $dish) {
                      return ($dish->in_stock ? 'sim' : 'não');
                  });
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title(__('ID'))
                ->field('id'),

            Column::add()
                ->title(__('Em Estoque'))
                ->toggleable(true, 'sim', 'não')
                ->makeBooleanFilter('in_stock', 'sim', 'não')
                ->field('in_stock'),
        ];
    }

    public function actionRules(): array
    {
        return $this->testActionRules;
    }
}
