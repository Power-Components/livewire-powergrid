<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Cache};

final class PowerGridTableCache
{
    private const THREE_HOURS = 60 * 60 * 3;

    private static string $cachedTablesListTag = 'pg_cached_tables';

    private static string $cachedTableTag = 'powergrid_columns_in_';

    public static function getOrCreate(string $tableName, callable $tableColumns): array
    {
        $tag = self::generateTag($tableName);

        if (Cache::has($tag)) {
            return (array) Cache::get($tag);
        }

        self::addToCachedTablesList($tag);

        /** @phpstan-ignore-next-line */
        return (array) Cache::remember($tag, self::THREE_HOURS, $tableColumns);
    }

    public static function forgetAll(): void
    {
        rescue(function (): void {
            /** @phpstan-ignore-next-line */
            self::list()->each(fn (string $tag): bool => Cache::forget($tag));

            Cache::forget('pg_cached_tables');
        }, report: false);
    }

    private static function list(): Collection
    {
        return collect(
            /** @phpstan-ignore-next-line */
            rescue(
                fn () => (Cache::get(self::$cachedTablesListTag) ?? []),
                [],
                report: false
            )
        );
    }

    private static function addToCachedTablesList(string $tag): void
    {
        rescue(fn () => Cache::put(self::$cachedTablesListTag, self::list()->push($tag)->unique()->toArray()), report: false);
    }

    private static function generateTag(string $tableName): string
    {
        return self::$cachedTableTag . $tableName;
    }
}
