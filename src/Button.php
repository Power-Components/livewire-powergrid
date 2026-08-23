<?php

namespace PowerComponents\LivewirePowerGrid;

/**
 * @see \PowerComponents\Turbine\Button
 */
class Button extends \PowerComponents\Turbine\Button
{
    /** Display order among header buttons (lower renders first; null keeps declaration order). */
    public ?int $order = null;

    public function order(int $order): static
    {
        $this->order = $order;

        return $this;
    }
}
