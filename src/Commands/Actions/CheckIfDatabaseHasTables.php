<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Support\Facades\Schema;

final class CheckIfDatabaseHasTables
{
    public static function handle(): bool
    {
        try {
            return count(Schema::getTables()) > 0 ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
