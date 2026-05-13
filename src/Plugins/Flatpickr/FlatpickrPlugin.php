<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Flatpickr;

use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;

class FlatpickrPlugin extends PluginBase
{
    public function name(): string
    {
        return 'flatpickr';
    }

    public function isEnabled(): bool
    {
        /** @var array $columns */
        $columns = $this->component->columns;

        return collect($columns)
            ->contains(function ($column) {
                /** @var array $filters */
                $filters = data_get($column, 'filters', []);

                return collect($filters)
                    ->contains(fn ($filter) => in_array(data_get($filter, 'type'), ['date_picker', 'datetime']));
            });
    }
}
