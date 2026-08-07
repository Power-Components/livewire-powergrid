<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use PowerComponents\LivewirePowerGrid\Column;

/**
 * Immutable per-column render metadata, resolved once per request. Row-invariant
 * values — including the theme-resolved `class`/`style` token strings — are
 * precomputed here so the per-cell render path does no repeated theme/data_get work.
 */
final readonly class ColumnViewModel
{
    /**
     * @param  Column|array<string, mixed>|\stdClass  $column  original column, for renderColumnContent()/plugins
     * @param  array<array-key, mixed>|string  $contentClasses
     * @param  array<string, mixed>  $customParams
     */
    public function __construct(
        public Column|array|\stdClass $column,
        public string $field,
        public string $dataField,
        public bool $isAction,
        public bool $index,
        public string $contentClassField,
        public array|string $contentClasses,
        public bool $hasCustomContent,
        public ?string $customView,
        public array $customParams,
        public string $tdClass,
        public string $tdStyle,
        public ?string $spanClassStatic,
    ) {}
}
