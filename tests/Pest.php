<?php
use Pest\PendingObjects\TestCall;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\TestCase;
use PowerComponents\LivewirePowerGrid\{
    Column,
    PowerGridComponent,
    Tests\DishesActionRulesTable,
    Tests\DishesActionTable,
    Tests\DishesCalculationsTable,
    Tests\DishesCollectionTable,
    Tests\DishesEnumTable,
    Tests\DishesTable,
    Tests\DishesTableWithJoin
};

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

dataset('enum', [
    'tailwind -> id'  => [DishesEnumTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id' => [DishesEnumTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
]);

dataset('themes', [
    'tailwind -> id'         => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id'        => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    'tailwind -> dishes.id'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.id']],
    'bootstrap -> dishes.id' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.id']],
]);

dataset('action', [
    'tailwind'       => [DishesActionTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishesActionTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishesActionTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishesActionTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

dataset('rules', [
    'tailwind'       => [DishesActionRulesTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishesActionRulesTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishesActionRulesTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishesActionRulesTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

dataset('calculations', [
    'tailwind'       => [DishesCalculationsTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishesCalculationsTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishesCalculationsTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishesCalculationsTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
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

/**
 * Skip tests based on mininum PHP Version
 *
 * @param string $version
 * @return TestCall|PhpUnitTestCase|mixed
 */
function onlyFromPhp(string $version)
{
    if (version_compare(PHP_VERSION, $version, '<')) {
        test()->markTestSkipped('This test requires PHP ' . $version);
    }

    return test();
}
