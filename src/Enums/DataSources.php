<?php

namespace PowerComponents\LivewirePowerGrid\Enums;

enum DataSources: string
{
    case ELOQUENT_BUILDER = 'Eloquent Builder';

    case QUERY_BUILDER = 'Query Builder';

    case COLLECTION = 'Collection';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case): array => [strval($case->name) => strval($case->value)])
            ->toArray();
    }
}
