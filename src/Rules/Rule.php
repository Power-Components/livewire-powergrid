<?php

namespace PowerComponents\LivewirePowerGrid\Rules;

use Illuminate\Support\Facades\Facade;

/**
 * @method static RuleActions button(string $action)
 * @method static RuleRows rows()
 * @method static RuleCheckbox checkbox()
 * @see \PowerComponents\LivewirePowerGrid\Rules\RuleManager
 */
class Rule extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'rule';
    }
}
