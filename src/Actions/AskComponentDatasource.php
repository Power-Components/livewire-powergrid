<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use function Laravel\Prompts\select;

use PowerComponents\LivewirePowerGrid\Enums\DataSources;

final class AskComponentDatasource
{
    public static function handle(): string
    {
        return strval(select(
            label: 'Select your preferred Data source:',
            options: DataSources::options(),
            default: DataSources::ELOQUENT_BUILDER->name
        ));
    }
}
