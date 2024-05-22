<?php

namespace PowerComponents\LivewirePowerGrid\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static RuleActions button(string $action)
 * @method static RuleToggleable toggleable(string $action)
 * @method static RuleEditOnClick editOnClick(string $action)
 * @method static RuleRows rows()
 * @method static RuleCheckbox checkbox()
 * @method static RuleRadio radio()
 * @mixin \PowerComponents\LivewirePowerGrid\Components\Rules\BaseRule
 * @mixin \PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager
 * @mixin \PowerComponents\LivewirePowerGrid\Components\Rules\RuleActions
 * @mixin \PowerComponents\LivewirePowerGrid\Components\Rules\RuleCheckbox
 * @mixin \PowerComponents\LivewirePowerGrid\Components\Rules\RuleEditOnClick
 * @mixin \PowerComponents\LivewirePowerGrid\Components\Rules\RuleRadio
 * @mixin \PowerComponents\LivewirePowerGrid\Components\Rules\RuleRows
 * @mixin \PowerComponents\LivewirePowerGrid\Components\Rules\RuleToggleable
 * @see RuleManager
 */
class Rule extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'rule';
    }
}
