<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Closure;
use InvalidArgumentException;
use Livewire\Wireable;

/**
 * @codeCoverageIgnore
 */
class BaseRule implements Wireable
{
    /** @var array<string, mixed> */
    public array $rule = [];

    public string $forAction = '';

    public string $column = '';

    private bool $hasCondition = false;

    public function setCondition(string $condition, Closure $closure): self
    {
        if ($this->hasCondition === true) {
            throw new InvalidArgumentException('A rule must have only one condition.');
        }

        $this->hasCondition = true;

        $this->rule[$condition] = $closure;

        return $this;
    }

    public function setModifier(string $modifier, mixed $arguments): void
    {
        $this->rule[$modifier] = $arguments;
    }

    /** @param  array<string, mixed>  $argument */
    public function pushModifier(string $modifier, array $argument): void
    {
        if (isset($this->rule[$modifier]) && is_array($this->rule[$modifier])) {
            $this->rule[$modifier][] = $argument;

            return;
        }

        $this->setModifier($modifier, [$argument]);
    }

    /** @return array<string, mixed> */
    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }

    public function when(Closure $closure): self
    {
        $this->setCondition('when', $closure);

        return $this;
    }

    public function unless(Closure $closure): self
    {
        $this->setCondition('unless', $closure);

        return $this;
    }
}
