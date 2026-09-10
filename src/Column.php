<?php

namespace PowerComponents\LivewirePowerGrid;

/**
 * @see \PowerComponents\Turbine\Column
 *
 * Plugin macros
 *
 * @method $this limit(int $characters, string $end = '...') Truncate the displayed value to N characters with an ellipsis (TruncatePlugin).
 * @method $this tooltip(bool $enabled = true, string $position = 'top') Show the full, untruncated value in a theme-aware tooltip (TruncatePlugin).
 * @method $this summarize(string $operation, string $label, bool $header = false, bool $footer = true) Built-in aggregate: sum, count, avg, min, or max.
 * @method $this withSummary(string $key, string $label, \Closure $using, bool $header = false, bool $footer = true) Custom summary closure.
 */
class Column extends \PowerComponents\Turbine\Column {}
