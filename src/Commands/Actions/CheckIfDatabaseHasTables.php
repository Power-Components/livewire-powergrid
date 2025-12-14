<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Exception;
use Illuminate\Support\Facades\Schema;

/** @codeCoverageIgnore */
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
