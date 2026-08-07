<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Enums;

/** Where a generated component's fields are read from. */
enum ColumnSource
{
    case FILLABLE;

    case DATABASE_TABLE;

    public static function from(string $columnSource): mixed
    {
        return constant("self::{$columnSource}");
    }
}
