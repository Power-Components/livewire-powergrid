<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\Schema;

final class ListDatabaseTables
{
    private const HIDDEN_TABLES = ['failed_jobs', 'migrations', 'password_reset_tokens', 'personal_access_tokens'];

    /**
     * List tables in database
     *
     */
    public static function handle(): array
    {
        try {
            return array_values(collect(Schema::getTables())
                ->pluck('name')
                ->diff(self::HIDDEN_TABLES)
                ->toArray());
        } catch (\Exception $e) {
            return [];
        }
    }
}
