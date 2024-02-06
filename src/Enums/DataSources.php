<?php

namespace PowerComponents\LivewirePowerGrid\Enums;

use Illuminate\Support\Collection;

enum DataSources: string
{
    case ELOQUENT_BUILDER = 'Eloquent Builder';

    case QUERY_BUILDER = 'Query Builder';

    case COLLECTION = 'Collection';

    /**
     * @return Collection<string, string>
     */
    public static function collect(): Collection
    {
        return collect(self::cases())->mapWithKeys(fn ($case): array => [$case->name => $case->value]);
    }
}
