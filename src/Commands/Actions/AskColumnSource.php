<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use PowerComponents\LivewirePowerGrid\Commands\Enums\ColumnSource;

use function Laravel\Prompts\select;

/** @codeCoverageIgnore */
final class AskColumnSource
{
    public static function handle(): string
    {
        $columnSource = ColumnSource::asOptions();

        $choice = strval(select(
            label: 'Where should the columns be generated from?',
            options: $columnSource->values()->toArray(), // @phpstan-ignore-line
            default: 0
        ));

        return (string) $columnSource->filter(function ($item) use ($choice) {
            return $item === $choice;
        })->keys()[0];
    }
}
