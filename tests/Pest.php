<?php

use PowerComponents\LivewirePowerGrid\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function filterInputText(string $text, string $type): array
{
    return [
        "input_text" => [
            "name" => $text
        ],
        "input_text_options" => [
            "name" => $type
        ]
    ];
}
