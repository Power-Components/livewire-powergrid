<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Truncate;

use Illuminate\Support\Arr;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;

/**
 * Adds `->limit()` and `->tooltip()` to columns.
 *
 * `->limit(24)` truncates the displayed value to 24 characters using an
 * ellipsis. `->tooltip()` renders the full, untruncated value in a
 * theme-aware tooltip (Flux uses <flux:tooltip>; other themes use a shared
 * Alpine tooltip with a native `title` fallback).
 *
 * This plugin only registers the macros — the actual truncation/tooltip
 * rendering is applied in the shared table row view so it composes with the
 * column's existing content classes and value handling instead of replacing
 * them (hence handles() stays false and no render() override is provided).
 */
class TruncatePlugin extends PluginBase
{
    public static function boot(): void
    {
        Column::macro('limit', function (int $characters, string $end = '...'): Column {
            /** @var Column $this */
            Arr::set($this->pluginData, 'truncate.limit', $characters);
            Arr::set($this->pluginData, 'truncate.end', $end);

            return $this;
        });

        Column::macro('tooltip', function (bool $enabled = true, string $position = 'top'): Column {
            /** @var Column $this */
            Arr::set($this->pluginData, 'truncate.tooltip', $enabled);
            Arr::set($this->pluginData, 'truncate.position', $position);

            return $this;
        });
    }

    public function name(): string
    {
        return 'truncate';
    }

    public function isEnabled(): bool
    {
        return collect($this->component->declaredColumns())
            ->contains(fn ($column) => ! empty(data_get($column, 'pluginData.truncate')));
    }
}
