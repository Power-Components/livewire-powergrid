<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\TestCase;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};

uses(TestCase::class)->in(__DIR__);

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

function filterNumber(string $field, ?string $min, ?string $max): array
{
    return [
        'number' => [
            $field => [
                'start' => $min,
                'end'   => $max,

            ],
        ],
    ];
}

function filterBoolean(string $field, ?string $value): array
{
    return [
        'boolean' => [
            $field => $value,
        ],
    ];
}

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

function skipOnSQLite()
{
    if (DB::getDriverName() === 'sqlite') {
        test()->markTestSkipped('This test requires MYSQL/PGSQL database');
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
