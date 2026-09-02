<?php

namespace PowerComponents\LivewirePowerGrid\Components\SetUp;

use PowerComponents\Turbine\Contracts\Definition;

final class Tab implements Definition
{
    public string $label = '';

    public string $icon = '';

    /** @var array<int, mixed>|null */
    public ?array $scope = null;

    /** true = auto count, false = hidden, int = fixed value */
    public bool|int $badge = true;

    public function __construct(public string $key) {}

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /** @param  array<int, mixed>  $scope  [column, value] or [column, operator, value] */
    public function scope(array $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    public function badge(bool|int $badge): self
    {
        $this->badge = $badge;

        return $this;
    }
}
