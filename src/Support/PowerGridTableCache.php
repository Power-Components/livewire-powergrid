<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class PowerGridTableCache
{
    private static function list(): Collection
    {
        $cachedTableTags = rescue(fn () => Cache::get('pg_cached_tables')) ?? '';

        /** @phpstan-ignore-next-line */
        return collect(json_decode($cachedTableTags, true));
    }

    public static function put(string $modelTable, array $tableColumns): array
    {
        $tag = 'powergrid_columns_in_' . $modelTable;

        Cache::put('pg_cached_tables', self::list()->push($tag)->unique()->toJson());

        return (array) Cache::remember(
            $tag,
            60 * 60 * 3,
            fn () => $tableColumns
        );
    }

    public static function forgetAll(): void
    {
        rescue(function (): void {
            /** @phpstan-ignore-next-line */
            self::list()->each(fn (string $tag): bool => Cache::forget($tag));

            Cache::forget('pg_cached_tables');
        });
    }
}
