<?php

namespace PowerComponents\LivewirePowerGrid\Contracts;

use Closure;

interface ConditionalRule
{
    public function setCondition(string $condition, Closure $closure): self;

    public function isValidModifier(string $modifier): bool;

    public function setModifier(string $modifier, mixed $arguments): void;

    public function pushModifier(string $modifier, array $argument): void;

    public function when(Closure $closure): self;

    public function unless(Closure $closure): self;

    public function toLivewire(): array;

    public static function fromLivewire($value);
}
