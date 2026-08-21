<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\FilterBuilder;

use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\LivewirePowerGrid\Themes\{DaisyUI, Flux};
use PowerComponents\Turbine\Components\Filters\FilterInputText;
use PowerComponents\Turbine\Plugins\FilterBuilder\FilterBuilderValidator;

class FilterBuilderPlugin extends PluginBase
{
    protected static ?string $cachedJs = null;

    public function name(): string
    {
        return 'filterBuilder';
    }

    public function isEnabled(): bool
    {
        return filled(data_get($this->component->setUp, 'filterBuilder'))
            && filled($this->component->filters());
    }

    public function handlesZone(string $zone): bool
    {
        return $zone === 'header' && $this->isEnabled() && $this->resolveThemeView() !== '';
    }

    public function renderZone(string $zone): ?string
    {
        if (! $this->handlesZone($zone)) {
            return null;
        }

        /** @var array{match: string, rows: array<int, mixed>} $applied */
        $applied = $this->component->filterBuilder;

        /** @var string $matchDefault */
        $matchDefault = data_get($this->component->setUp, 'filterBuilder.match', 'and');

        /** @var view-string $view */
        $view = $this->resolveThemeView();

        return view($view, [
            'element' => $this->component->headerElement('filterBuilder'),
            'tableName' => $this->component->tableName,
            'columns' => $this->component->filterBuilderMeta(),
            'operatorLabels' => $this->operatorLabels(),
            'valuelessOperators' => FilterBuilderValidator::VALUELESS_OPERATORS,
            'rangeOperators' => FilterBuilderValidator::RANGE_OPERATORS,
            'applied' => $applied,
            'matchDefault' => $matchDefault,
            'appliedCount' => count($applied['rows'] ?? []),
            'js' => $this->js(),
        ])->render();
    }

    /**
     * Resolve the trigger/modal view for the active theme.
     *
     * A theme may point at its own view through the `header.filter_builder.view`
     * token; otherwise the packaged variant for the theme is used. Themes with no
     * variant available render nothing, keeping the grid untouched.
     */
    private function resolveThemeView(): string
    {
        $tokenView = theme_view('header.filter_builder');

        if ($tokenView !== '' && view()->exists($tokenView)) {
            return $tokenView;
        }

        $theme = app()->bound('powergrid.theme') ? app('powergrid.theme') : null;

        $variant = match (true) {
            $theme instanceof Flux => 'flux',
            $theme instanceof DaisyUI => 'daisyui',
            default => 'index',
        };

        $view = "powergrid-plugins::FilterBuilder.themes.{$variant}";

        return view()->exists($view) ? $view : '';
    }

    /**
     * @return array<string, string>
     */
    private function operatorLabels(): array
    {
        $labels = [];

        foreach (FilterInputText::getInputTextOperators() as $operator) {
            $labels[$operator] = strval(trans("livewire-powergrid::datatable.input_text_options.$operator"));
        }

        $labels['between'] = strval(trans('livewire-powergrid::datatable.filter_builder.number.between'));
        $labels['greater_equal'] = strval(trans('livewire-powergrid::datatable.filter_builder.number.greater_equal'));
        $labels['less_equal'] = strval(trans('livewire-powergrid::datatable.filter_builder.number.less_equal'));

        return $labels;
    }

    private function js(): string
    {
        if (self::$cachedJs === null) {
            self::$cachedJs = strval(file_get_contents(__DIR__.'/index.js'));
        }

        return self::$cachedJs;
    }
}
