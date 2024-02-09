<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use function Laravel\Prompts\select;

use PowerComponents\LivewirePowerGrid\Enums\DataSource;

final class AskComponentDatasource
{
    public static function handle(): string
    {
        return strval(select(
            label: 'Select your preferred Data source:',
            options: DataSource::asOptions(),
            default: DataSource::ELOQUENT_BUILDER->name
        ));
    }
}
