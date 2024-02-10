<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\{error, info, suggest};

final class AskDatabaseTableName
{
    public static function handle(): string
    {
        $tableExists = false;

        while (!$tableExists) {
            $tableName = suggest(
                label: 'Enter or Select a DB Table',
                options: ListDatabaseTables::handle(),
                required: true,
            );

            if (CheckIfDatabaseHasTables::handle() === false) {
                $tableExists = true; // Assuming user is creating component before migrating DB.

                info('🚫 Database seems to be empty. Aborting Database related steps!');
            } else {
                $tableExists = Schema::hasTable($tableName);

                if (!$tableExists) {
                    error("The table [{$tableName}] does not exist! Try again or press Ctrl+C to abort.");
                }
            }
        }

        return $tableName;
    }
}
