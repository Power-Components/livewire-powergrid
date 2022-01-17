<?php
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\TestCase;
use PowerComponents\LivewirePowerGrid\{Column,
    PowerGridComponent,
    Tests\DishesCollectionTable,
    Tests\DishesTable,
    Tests\DishesTableWithJoin};

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

function filterInputText(string $text, string $type, $field = 'name'): array
{
    return [
        'input_text' => [
            $field => $text,
        ],
        'input_text_options' => [
            $field => $type,
        ],
    ];
}

dataset('themes', [
    [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.id']],
    [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.id']],
]);

dataset('themes with name field', [
    [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'name']],
    [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'name']],
    [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.name']],
    [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.name']],
]);

dataset('themes with collection table', [
    [DishesCollectionTable::class, 'tailwind'],
    [DishesCollectionTable::class, 'bootstrap'],
]);
