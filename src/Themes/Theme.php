<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

abstract class Theme
{
    protected array $tokens = [];

    public static function make(): static
    {
        return new static();
    }

    abstract public function struct(): array;

    public function views(): array
    {
        return [];
    }

    public function merge(array $overrides): static
    {
        $this->tokens = array_replace_recursive($this->resolveTokens(), $overrides);

        return $this;
    }

    public function resolveTokens(): array
    {
        if (empty($this->tokens)) {
            $this->tokens = $this->struct();
        }

        return $this->tokens;
    }

    public function get(string $key, string $default = ''): string
    {
        return strval(data_get($this->resolveTokens(), $key, $default));
    }

    public function resolveView(string $alias): string
    {
        $views = $this->views();

        if (isset($views[$alias])) {
            return $views[$alias];
        }

        $tailwindViews = (new Tailwind())->views();

        return $tailwindViews[$alias] ?? '';
    }
}
