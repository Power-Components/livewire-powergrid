<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

use Closure;

trait HasProperties
{
    /** @var array<string, mixed> */
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

    /** @param  array<string, mixed>  $properties */
    public function fill(array $properties): self
    {
        foreach ($properties as $key => $value) {
            $this->properties[$key] = $value;
        }

        return $this;
    }

    /**
     * @param  Body|Checkbox|Cols|Footer|Header|HeaderButton|Layout|Pagination|Radio|SearchBox|Table|Tabs  $component
     * @param  Closure|array<string, mixed>  $callback
     * @return array<string, mixed>
     */
    protected function buildSection(object $component, Closure|array $callback, bool $inheritBaseView = false): array
    {
        if ($inheritBaseView && $this->baseView) {
            $component->setBaseView($this->baseView);
        }

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        return $component->toArray();
    }

    /** @return array<string, mixed> */
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
