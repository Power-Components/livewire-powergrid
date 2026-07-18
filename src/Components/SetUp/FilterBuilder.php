<?php

namespace PowerComponents\LivewirePowerGrid\Components\SetUp;

use Livewire\Wireable;

/**
 * Opt-in + configuration for the Filter Builder plugin (Flux-only modal).
 *
 * This object ONLY toggles/configures the feature. The list of filterable
 * columns, their types and operators come from the component's filters()
 * definitions — never redeclared here.
 */
final class FilterBuilder implements Wireable
{
    public const MATCH_AND = 'and';

    public const MATCH_OR = 'or';

    public string $name = 'filterBuilder';

    public string $match = self::MATCH_AND;

    public int $maxConditions = 30;

    public bool $hideDefaultFilters = false;

    /** @var list<string> Restrict which filters() columns appear (empty = all). */
    public array $only = [];

    /** @var list<string> Hide specific filters() columns from the builder. */
    public array $except = [];

    /**
     * Default match mode used when the modal opens with no conditions yet.
     */
    public function match(string $match): self
    {
        $this->match = $match === self::MATCH_OR ? self::MATCH_OR : self::MATCH_AND;

        return $this;
    }

    /**
     * Hard cap on how many conditions may be applied (security / anti-abuse).
     */
    public function maxConditions(int $max): self
    {
        $this->maxConditions = max(1, $max);

        return $this;
    }

    /**
     * Hide the default inline/outside filters (per-column inputs, the "Filters"
     * toggle button and the outside filter panel) so the builder becomes the
     * single filtering UI. The enabled-filter pills stay visible.
     */
    public function hideDefaultFilters(bool $hide = true): self
    {
        $this->hideDefaultFilters = $hide;

        return $this;
    }

    /** @param  list<string>  $fields */
    public function only(array $fields): self
    {
        $this->only = array_values($fields);

        return $this;
    }

    /** @param  list<string>  $fields */
    public function except(array $fields): self
    {
        $this->except = array_values($fields);

        return $this;
    }

    /** @return array<string, mixed> */
    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value): self
    {
        $instance = new self();

        foreach ((array) $value as $key => $val) {
            if (property_exists($instance, $key)) {
                $instance->{$key} = $val;
            }
        }

        return $instance;
    }
}
