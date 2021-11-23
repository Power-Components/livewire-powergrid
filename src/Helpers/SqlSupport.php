<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Support\Facades\DB;

class SqlSupport
{
    public static function like(): string
    {
        $driverName = DB::getDriverName();

        $likeSyntax = [
            'pgsql' => 'ILIKE',
        ];

        return $likeSyntax[$driverName] ?? 'LIKE';
    }
}
