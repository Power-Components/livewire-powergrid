<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Exception;
use Illuminate\Support\Facades\Schema;

final class CheckIfDatabaseHasTables
{
    public static function handle(): bool
    {
        try {
            return count(Schema::getTables()) > 0;
        } catch (Exception) {
            return false;
        }
    }
}
