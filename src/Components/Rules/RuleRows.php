<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Closure;
use Livewire\Wireable;

class RuleRows implements Wireable
{
    public array $rule = [];

    public string $forAction = RuleManager::TYPE_ROWS;

    public ?string $column = '';

    /**
     * Disables the button.
     */
    public function when(Closure $closure = null): RuleRows
    {
        $this->rule['when'] = $closure;

        return $this;
    }

    /**
     * Sets the button's given attribute to the given value.
     */
    public function setAttribute(string $attribute = null, string $value = null): RuleRows
    {
        $this->rule['setAttribute'] = [
            'attribute' => $attribute,
            'value'     => $value,
        ];

        return $this;
    }

    /**
     * Sets the button's given attribute to the given value.
     */
    public function detailView(string $detailView = null, array $options = []): RuleRows
    {
        $this->rule['detailView'] = [
            'detailView' => $detailView,
            'options'    => $options,
        ];

        return $this;
    }

    /**
     * Show the toggleable in current row.
     */
    public function showToggleable(): RuleRows
    {
        $this->rule['showHideToggleable'] = 'show';

        return $this;
    }

    /**
     * Hide the toggleable in current row.
     */
    public function hideToggleable(): RuleRows
    {
        $this->rule['showHideToggleable'] = 'hide';

        return $this;
    }

    /**
     * Interacts with Blade loop.
     */
    public function loop(Closure $closure = null): RuleRows
    {
        $this->rule['loop'] = $closure;

        return $this;
    }

    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }
}
