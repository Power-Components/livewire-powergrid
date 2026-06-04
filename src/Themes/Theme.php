<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\PowerGridManager;
use PowerComponents\LivewirePowerGrid\Themes\Components\ThemeBuilder;

abstract class Theme
{
    protected array $tokens = [];

    protected ?string $parentTheme = null;

    public static function make(): static
    {
        return new static();
    }

    abstract public function struct(): array|ThemeBuilder;

    public function name(): string
    {
        return strval(str(static::class)->afterLast('\\')->kebab());
    }

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
            $struct = $this->struct();

            $tokens = $struct instanceof ThemeBuilder
                ? $struct->toArray()
                : $struct;

            if ($this->parentTheme && $this->parentTheme !== static::class) {
                $parentTokens = (new $this->parentTheme())->resolveTokens();
                $tokens = array_replace_recursive($parentTokens, $tokens);
            }

            // Merge theme-level method overrides (e.g. filter())
            foreach ($this->themeTokenMethods() as $method) {
                if (method_exists($this, $method)) {
                    $extra = $this->{$method}();
                    if (! empty($extra)) {
                        $tokens = array_replace_recursive($tokens, $extra);
                    }
                }
            }

            // Merge plugin-contributed theme tokens
            foreach (PowerGridManager::$plugins as $plugin) {
                $pluginTokens = $plugin::themeTokens();
                if (! empty($pluginTokens)) {
                    $tokens = array_replace_recursive($tokens, $pluginTokens);
                }
            }

            $this->tokens = $tokens;
        }

        return $this->tokens;
    }

    public function resolveView(string $alias): string
    {
        $views = $this->views();

        if (isset($views[$alias])) {
            return $views[$alias];
        }

        $tokens = $this->resolveTokens();
        $aliasNormalized = str_replace('-', '_', $alias);
        $baseView = data_get($tokens, 'base_view');

        $tokenView = data_get($tokens, $aliasNormalized);

        if (empty($tokenView) || ! is_string($tokenView)) {
            $path = ! str_contains($aliasNormalized, '.')
                ? 'view_'.$aliasNormalized
                : str_replace('.', '.view_', $aliasNormalized);

            $tokenView = data_get($tokens, $path);
        }

        if (empty($tokenView) || ! is_string($tokenView)) {
            $tokenView = data_get($tokens, $aliasNormalized.'.view');
        }

        if (empty($tokenView) && str_contains($aliasNormalized, 'search')) {
            $path = str_replace('search', 'search_box', $aliasNormalized).'.view';
            $tokenView = data_get($tokens, $path);
        }

        if (empty($tokenView) || ! is_string($tokenView)) {
            foreach (['header', 'table', 'footer'] as $prefix) {
                $tokenView = data_get($tokens, $prefix.'.view_'.$aliasNormalized)
                    ?? data_get($tokens, $prefix.'.'.$aliasNormalized.'.view');

                if (is_string($tokenView) && ! empty($tokenView)) {
                    break;
                }
            }
        }

        if (is_string($tokenView) && ! empty($tokenView)) {
            if ($baseView && ! str_contains($tokenView, '::') && ! view()->exists($tokenView)) {
                $candidate = rtrim($baseView, '.').'.'.str_replace(['_', '/'], '.', $tokenView);
                if (view()->exists($candidate)) {
                    return $candidate;
                }
            }

            return $tokenView;
        }

        if ($baseView) {
            $baseViewPrefix = rtrim($baseView, '.').'.';

            $candidateDots = $baseViewPrefix.$alias;
            if (view()->exists($candidateDots)) {
                return $candidateDots;
            }

            $candidateDashes = $baseViewPrefix.str_replace(['_', '.'], '-', $aliasNormalized);
            if (view()->exists($candidateDashes)) {
                return $candidateDashes;
            }
        }

        if ($this->parentTheme && $this->parentTheme !== static::class) {
            return (new $this->parentTheme())->resolveView($alias);
        }

        $structurePath = 'livewire-powergrid::components.structure.';

        $candidateStructureDots = $structurePath.$alias;
        if (view()->exists($candidateStructureDots)) {
            return $candidateStructureDots;
        }

        $candidateStructureDashes = $structurePath.str_replace(['_', '.'], '-', $aliasNormalized);
        if (view()->exists($candidateStructureDashes)) {
            return $candidateStructureDashes;
        }

        return '';
    }

    public function get(string $key, string $default = ''): string
    {
        $value = data_get($this->resolveTokens(), $key, $default);

        if (is_array($value)) {
            return $default;
        }

        return strval($value);
    }

    /**
     * Returns theme token method names that this theme class provides.
     * Override in subclasses to add custom token providers.
     */
    protected function themeTokenMethods(): array
    {
        return ['filter', 'editable', 'toggleable'];
    }

    public function editable(): array
    {
        return [];
    }

    public function filter(): array
    {
        return [];
    }

    public function toggleable(): array
    {
        return [];
    }
}
