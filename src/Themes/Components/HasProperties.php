<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

trait HasProperties
{
    protected array $properties = [];

    protected ?string $baseView = null;

    public function setBaseView(string $baseView): self
    {
        if (filled($baseView) && ! str_ends_with($baseView, '.')) {
            $baseView .= '.';
        }

        $this->baseView = $baseView;

        return $this;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->properties as $key => $value) {
            $snakeKey = str($key)->snake()->toString();

            if ($this->baseView && str_starts_with($snakeKey, 'view') && is_string($value) && ! str_contains($value, '::')) {
                $value = $this->baseView.$value;
            }

            $result[$snakeKey] = $value;
        }

        return $result;
    }
}
