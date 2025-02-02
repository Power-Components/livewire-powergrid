<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use function Laravel\Prompts\select;

use PowerComponents\LivewirePowerGrid\Commands\Enums\Datasource;

final class AskComponentDatasource
{
    public static function handle(): string
    {
        // Must pass options as array<int, "label"> to
        // improve users experience when Laravel prompt falls back.
        $datasource = Datasource::asOptions();

        $choice = strval(select(
            label: 'Select your preferred Data source:',
            options: $datasource->values()->toArray(), // @phpstan-ignore-line
            default: 0
        ));

        // Find and return they key based on user's choice.
        return (string) $datasource->filter(function ($item) use ($choice) {
            return $item === $choice;
        })->keys()[0];
    }
}
