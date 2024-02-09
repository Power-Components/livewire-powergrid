<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use function Laravel\Prompts\confirm;

final class ConfirmGetFromFillable
{
    public static function handle(): bool
    {
        return confirm(
            label: 'Add table columns based on Model\'s $fillable property?'
        );
    }
}
