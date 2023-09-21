<?php

namespace PowerComponents\LivewirePowerGrid\Facades;

use Illuminate\Support\Facades\Facade;
use PowerComponents\LivewirePowerGrid\Components\Rules\{RuleActions, RuleCheckbox, RuleManager, RuleRows};

/**
 * @method static RuleActions button(string $action)
 * @method static RuleRows rows()
 * @method static RuleCheckbox checkbox()
 * @see RuleManager
 */
class Rule extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'rule';
    }
}
