<?php

use Illuminate\Support\Str;

function getLaravelDir(): string
{
    return __DIR__ . '/../vendor/orchestra/testbench-core/laravel/';
}

function expectInputText(object $params, mixed $component, string $value, string $type)
{
    if (str_contains($params->field, '.')) {
        $data  = Str::of($params->field)->explode('.');
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
