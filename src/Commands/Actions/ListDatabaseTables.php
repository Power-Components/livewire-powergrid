<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Exception;
use Illuminate\Support\Facades\Schema;

final class ListDatabaseTables
{
    private const HIDDEN_TABLES = ['failed_jobs', 'migrations', 'password_reset_tokens', 'personal_access_tokens'];

    /**
     * List tables in database
     *
     * @return list<string>
     */
    public static function handle(): array
    {
        try {
            /** @var array<int, string> $tables */
            $tables = collect(Schema::getTables())
                ->pluck('name')
                ->diff(self::HIDDEN_TABLES)
                ->all();

            return array_values($tables);
        } catch (Exception) {
            return [];
        }
    }
}
