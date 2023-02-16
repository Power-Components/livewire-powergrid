<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\TestCase;
use PowerComponents\LivewirePowerGrid\{Column,
    PowerGridComponent,
    Tests\DishesActionRulesTable,
    Tests\DishesArrayTable,
    Tests\DishesCalculationsTable,
    Tests\DishesCollectionTable,
    Tests\DishesDynamicFiltersTable,
    Tests\DishesRowIndex,
    Tests\DishesSearchableRawTable,
    Tests\DishesSoftDeletesTable,
    Tests\DishesTable,
    Tests\DishesTableWithJoin};

uses(TestCase::class)->in(__DIR__);

Pest\Plugin::uses(\PowerComponents\LivewirePowerGrid\Tests\PowergridPlugin::class);

function getLaravelDir(): string
{
    return __DIR__ . '/../vendor/orchestra/testbench-core/laravel/';
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
            ->sortable(),
    ];

    $component             = new PowerGridComponent(1);
    $component->datasource = Dish::query();
    $component->columns    = $columns;

    return $component;
}

function filterInputText(string $text, string $type, $field = 'name'): array
{
    if (str_contains($field, '.')) {
        $data  = Str::of($field)->explode('.');
        $table = $data->get(0);
        $field = $data->get(1);

        return [
            'input_text' => [
                $table => [
                    $field => $text,
                ],
            ],
            'input_text_options' => [
                $table => [
                    $field => $type,
                ],
            ],
        ];
    }

    return [
        'input_text' => [
            $field => $text,
        ],
        'input_text_options' => [
            $field => $type,
        ],
    ];
}

function expectInputText(object $params, mixed $component, string $value, string $type): void
{
    if (str_contains($params->field, '.')) {
        $data  = Str::of($params->field)->explode('.');
        $table = $data->get(0);
        $field = $data->get(1);

        expect($component->filters)
            ->toMatchArray([
                'input_text' => [
                    $table => [
                        $field => $value,
                    ],
                ],
                'input_text_options' => [
                    $table => [
                        $field => $type,
                    ],
                ],
            ]);
    } else {
        expect($component->filters)
            ->toMatchArray([
                'input_text' => [
                    $params->field => $value,
                ],
                'input_text_options' => [
                    $params->field => $type,
                ],
            ]);
    }
}

dataset('themes', [
    'tailwind -> id'         => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id'        => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    'tailwind -> dishes.id'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.id']],
    'bootstrap -> dishes.id' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.id']],
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
    'tailwind'       => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'name']],
    'bootstrap'      => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'name']],
    'tailwind join'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.name']],
    'bootstrap join' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.name']],
]);

dataset('themes with array table', [
    [DishesArrayTable::class, 'tailwind'],
    [DishesArrayTable::class, 'bootstrap'],
]);

dataset('themes with collection table', [
    'tailwind' => [DishesCollectionTable::class, 'tailwind'],
    'bootsrap' => [DishesCollectionTable::class, 'bootstrap'],
]);

dataset('searchable-raw', [
    'tailwind'  => [DishesSearchableRawTable::class, (object) ['theme' => 'tailwind']],
    'bootstrap' => [DishesSearchableRawTable::class, (object) ['theme' => 'bootstrap']],
]);

dataset('themes with softDeletes', [
    [DishesSoftDeletesTable::class, 'tailwind'],
    [DishesSoftDeletesTable::class, 'bootstrap'],
]);

dataset('row index', [
    'tailwind'  => [DishesRowIndex::class, 'tailwind'],
    'bootstrap' => [DishesRowIndex::class, 'bootstrap'],
]);

dataset('themes with dynamic filter table', [
    'tailwind'  => [DishesDynamicFiltersTable::class, (object) ['theme' => 'tailwind']],
    'bootstrap' => [DishesDynamicFiltersTable::class, (object) ['theme' => 'bootstrap']],
]);

function requiresMySQL()
{
    if (DB::getDriverName() !== 'mysql') {
        test()->markTestSkipped('This test requires MySQL database');
    }

    return test();
}

function requiresSQLite()
{
    if (DB::getDriverName() !== 'sqlite') {
        test()->markTestSkipped('This test requires SQLite database');
    }

    return test();
}

function requiresPostgreSQL()
{
    if (DB::getDriverName() !== 'pgsql') {
        test()->markTestSkipped('This test requires PostgreSQL database');
    }

    return test();
}

function requiresOpenSpout()
{
    $isInstalled = \Composer\InstalledVersions::isInstalled('openspout/openspout');

    if (!$isInstalled) {
        test()->markTestSkipped('This test requires openspout/openspout');
    }

    return test();
}
