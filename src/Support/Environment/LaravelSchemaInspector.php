<?php

namespace PowerComponents\LivewirePowerGrid\Support\Environment;

use Illuminate\Support\Facades\Schema;
use PowerComponents\LivewirePowerGrid\Contracts\SchemaInspector;
use PowerComponents\LivewirePowerGrid\Support\PowerGridTableCache;

final class LaravelSchemaInspector implements SchemaInspector
{
    /** @return array<string, string> */
    public function columnTypes(string $table, ?string $connection = null): array
    {
        /** @var array<string, string> $types */
        $types = PowerGridTableCache::getOrCreate(
            $table,
            fn (): array => collect(Schema::connection($connection)->getColumns($table))
                ->pluck('type', 'name')
                ->toArray()
        );

        return $types;
    }

    /** @return list<string> */
    public function columnListing(string $table, ?string $connection = null): array
    {
        return Schema::connection($connection)->getColumnListing($table);
    }
}
