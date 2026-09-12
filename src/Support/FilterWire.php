<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use Illuminate\View\ComponentAttributeBag;
use InvalidArgumentException;

final class FilterWire
{
    /** @var array<string, string> slot => path inside the filter record */
    private const array PATHS = [
        'value' => 'value',
        'operator' => 'op',
        'start' => 'value.start',
        'end' => 'value.end',
        'formatted' => 'value.formatted',
    ];

    /** @var array<string, array<string, string>> filter type => slot => wire:model modifiers */
    private const array SLOTS = [
        'input_text' => ['value' => 'live.debounce.600ms', 'operator' => 'live.debounce.600ms'],
        'number' => ['start' => 'live.debounce.600ms', 'end' => 'live.debounce.600ms'],
        'select' => ['value' => 'live'],
        'boolean' => ['value' => 'live'],
        'multi_select' => ['value' => ''],
        'date' => ['formatted' => ''],
        'datetime' => ['formatted' => ''],
    ];

    /**
     * @param  array<string, mixed>  $filter
     * @param  array<string, array<string, string>>  $overrides  type => slot => modifiers
     * @return array<string, mixed>
     */
    public static function forView(array $filter, bool $deferred, array $overrides = []): array
    {
        $filter = FilterKey::withModelKey($filter);
        $filter['deferred'] = $deferred;
        $filter['filtersProperty'] = $deferred ? 'draftFilters' : 'filters';

        $type = $filter['key'] ?? null;

        if (is_string($type) && isset(self::SLOTS[$type])) {
            $filter['wire'] = self::bags($type, $filter, $deferred, $overrides);
        }

        return $filter;
    }

    /**
     * @param  mixed  $filter  Filter definition (array/object) or a resolved model key (string).
     * @param  array<string, array<string, string>>  $overrides  type => slot => modifiers
     * @return array<string, ComponentAttributeBag>
     */
    public static function bags(string $type, mixed $filter, bool $deferred = false, array $overrides = []): array
    {
        $slots = self::slotsFor($type, $overrides);
        $key = is_string($filter) ? $filter : FilterKey::fromFilter($filter);
        $wire = [];

        foreach ($slots as $slot => $modifiers) {
            $path = $key.'.'.self::PATHS[$slot];

            $wire[$slot] = new ComponentAttributeBag(
                $deferred
                    ? FilterKey::draftModel($path)
                    : FilterKey::liveModel($path, $modifiers),
            );
        }

        return $wire;
    }

    /**
     * @param  array<string, array<string, string>>  $overrides
     * @return array<string, string>
     */
    private static function slotsFor(string $type, array $overrides): array
    {
        $slots = self::SLOTS[$type] ?? throw new InvalidArgumentException("Unknown filter type [{$type}].");

        /** @var array<string, array<string, string>> $configured */
        $configured = (array) config('livewire-powergrid.filter_wire', []);

        foreach ([$configured[$type] ?? [], $overrides[$type] ?? []] as $layer) {
            foreach ((array) $layer as $slot => $modifiers) {
                if (! isset(self::PATHS[$slot])) {
                    throw new InvalidArgumentException(
                        "Unknown filter slot [{$slot}] for [{$type}]. Available: ".implode(', ', array_keys(self::PATHS)).'.'
                    );
                }

                $slots[$slot] = is_string($modifiers) ? trim($modifiers, '.') : '';
            }
        }

        return $slots;
    }
}
