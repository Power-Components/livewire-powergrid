<?php
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\TestCase;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};

uses(TestCase::class)->in(__DIR__);

function getLaravelDir(): string
{
    return  __DIR__ . '/../vendor/orchestra/testbench-core/laravel/';
}

function powergrid(): PowerGridComponent
{
    $columns = [
        Column::add()
            ->title('Id')
            ->field('id')
            ->searchable()
            ->sortable(),

        Column::add()
            ->title('Name')
            ->field('name')
            ->searchable()
            ->editOnClick(true)
            ->clickToCopy(true)
            ->makeInputText('name')
            ->sortable(),
    ];

    $component             = new PowerGridComponent(1);
    $component->datasource = Dish::query();
    $component->columns    = $columns;
    $component->perPage    = 10;

    return $component;
}

function powergridJoinCategory()
{
    $columns = [
        Column::add()
            ->title('Id')
            ->field('id')
            ->searchable()
            ->sortable(),

        Column::add()
            ->title('Name')
            ->field('name')
            ->searchable()
            ->editOnClick(true)
            ->clickToCopy(true)
            ->makeInputText('name')
            ->sortable(),

        Column::add()
            ->title('Name')
            ->field('category_name')
            ->sortable(),
    ];

    $component             = new PowerGridComponent(1);
    $component->datasource = Dish::query()->join('categories', function ($categories) {
        $categories->on('dishes.category_id', '=', 'categories.id');
    })->select('dishes.*', 'categories.name as category_name');
    $component->columns = $columns;
    $component->perPage = 10;

    return $component;
}
