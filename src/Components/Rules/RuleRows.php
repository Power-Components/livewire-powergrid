<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Closure;

class RuleRows extends BaseRule
{
    public string $forAction = RuleManager::TYPE_ROWS;

    protected string $usingLoop = '';

    public function setAttribute(string $attribute = null, string $value = null): RuleRows
    {
        $this->setModifier('setAttribute', [
            'attribute' => $attribute,
            'value'     => $value,
        ]);

        return $this;
    }

    /**
     * Sets the button's given attribute to the given value.
     */
    public function detailView(string $detailView = null, array $options = []): self
    {
        $this->setModifier('detailView', [
            'detailView' => $detailView,
            'options'    => $options,
        ]);

        return $this;
    }

    /**
     * Show all toggleables in current row.
     */
    public function showToggleable(): self
    {
        $this->setModifier('ToggleableVisibility', 'show');

        return $this;
    }

    /**
     * Hide all toggleables in current row.
     */
    public function hideToggleable(): self
    {
        $this->setModifier('ToggleableVisibility', 'hide');

        return $this;
    }

    /**
     * Enable all edit on click in current row.
     */
    public function enableEditOnClick(): self
    {
        $this->setModifier('EditOnClickVisibility', 'show');

        return $this;
    }

    /**
     * Disable all edit on click in current row.
     */
    public function disableEditOnClick(): self
    {
        $this->setModifier('EditOnClickVisibility', 'hide');

        return $this;
    }

    /**
     * Show the Detail toggle in current row.
     */
    public function showToggleDetail(): self
    {
        $this->setModifier('ToggleDetailVisibility', 'show');

        return $this;
    }

    /**
     * Hide the Detail toggle in current row.
     */
    public function hideToggleDetail(): self
    {
        $this->setModifier('ToggleDetailVisibility', 'hide');

        return $this;
    }

    /**
     * Interacts with Blade loop.
     */
    public function loop(Closure $closure): self
    {
        $this->setCondition('loop', $closure);

        return $this;
    }

    public function firstOnPage(): self
    {
        $this->setCondition('loop', fn ($loop) => $loop->first === true);

        return $this;
    }

    public function lastOnPage(): self
    {
        $this->setCondition('loop', fn ($loop) => $loop->last === true);

        return $this;
    }

    public function alternating(): self
    {
        $this->setCondition('loop', fn ($loop) => $loop->even);

        return $this;
    }
}
