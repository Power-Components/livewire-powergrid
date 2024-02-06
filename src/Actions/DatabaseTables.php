<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\Schema;

class DatabaseTables
{
    /**
     * List tables in database
     *
     */
    public static function list(): array
    {
        $driverName = Schema::getConnection()->getDriverName();
        $tables     = Schema::connection($driverName)->getTables();

        return collect($tables)
            ->map(fn ($table) => collect($table)->first()) /** @phpstan-ignore-line */
            ->toArray();
    }
}
