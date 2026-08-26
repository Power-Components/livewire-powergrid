<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use PowerComponents\Turbine\Column;

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
        public ?string $align,
        public string $alignClasses,
    ) {}

    public static function alignmentClasses(mixed $align): string
    {
        return match ($align) {
            'center' => 'justify-center text-center',
            'right', 'end' => 'justify-end text-right',
            default => 'justify-start text-left',
        };
    }
}
