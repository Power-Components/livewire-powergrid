<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\{error, suggest};

final class AskDatabaseTableName
{
    public static function handle(): string
    {
        $exists = false;

        while (!$exists) {
            $tableName = suggest(
                label: "Select or enter your Database Table's name:",
                options: DatabaseTables::list(),
                required: true
            );

            $exists = Schema::hasTable($tableName);

            if (!$exists && !app()->runningUnitTests()) {
                error("The table [{$tableName}] does not exist! Try again or press Ctrl+C to abort.");
            }
        }

        return $tableName;
    }
}
