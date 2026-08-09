<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

function getLaravelDir(): string
{
    return __DIR__.'/../vendor/orchestra/testbench-core/laravel/';
}

/**
 * Creates the `waiters` table backing the column generator tests.
 *
 * It deliberately mixes columns that are fillable, hidden, sensitive and
 * database-only, so the different field sources can be told apart.
 */
function createWaitersTable(): void
{
    Schema::dropIfExists('waiters');

    Schema::create('waiters', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->string('password');
        $table->string('remember_token')->nullable();
        $table->string('internal_note')->nullable();
        $table->integer('tips');
        $table->date('hired_at');
        $table->timestamps();
    });
}

function expectInputText(object $params, mixed $component, string $value, string $type)
{
    if (str_contains($params->field, '.')) {
        $data = Str::of($params->field)->explode('.');
        $table = $data->get(0);
        $field = $data->get(1);

        return expect($component->filters)
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
    }

    return expect($component->filters)
        ->toMatchArray([
            'input_text' => [
                $params->field => $value,
            ],
            'input_text_options' => [
                $params->field => $type,
            ],
        ]);
}

function filterInputText(string $text, string $type, $field = 'name'): array
{
    if (str_contains($field, '.')) {
        $data = Str::of($field)->explode('.');
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

function filterNumber(string $field, ?string $min, ?string $max): array
{
    return [
        'number' => [
            $field => [
                'start' => $min,
                'end' => $max,

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
