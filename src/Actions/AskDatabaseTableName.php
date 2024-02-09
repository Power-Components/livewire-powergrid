<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\{error, suggest};

final class AskDatabaseTableName
{
    public static function handle(): string
    {
        $exists = false;
        dump('loop');
        while (!$exists) {
            $tableName = suggest(
                label: 'Enter or Select a database table to import $fillable columns',
                options: ListDatabaseTables::handle(),
                default: 'users',
                required: true,
            );
            $exists = true;
            $exists = Schema::hasTable($tableName);

            if (!$exists) {
                error("The table [{$tableName}] does not exist! Try again or press Ctrl+C to abort.");
            }
        }

        return $tableName;
    }
}
