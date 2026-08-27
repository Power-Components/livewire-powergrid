<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\Turbine\Components\Rules\RuleManager;

final class RowRenderer
{
    public const string DEFAULT_ROW_VIEW = 'livewire-powergrid::components.themes.tailwind.table.row';

    public function __construct(private readonly PowerGridComponent $component) {}

    /** @var array<string, bool> memoized guard result, keyed by theme identity */
    private static array $directCache = [];

    public static function canRenderDirect(PowerGridComponent $component): bool
    {
        $theme = app()->bound('powergrid.theme') ? app('powergrid.theme')::class : 'default';

        return self::$directCache[$theme] ??= theme_view('table.row') === self::DEFAULT_ROW_VIEW;
    }

    public function render(object $row, int $rowIndex, ?int $childIndex, mixed $parentId, string|int $rowId): string
    {
        $html = '';

        if (isset($this->component->setUp['responsive'])) {
            $html .= $this->renderResponsiveToggle($rowId);
        }

        if (data_get($this->component->setUp, 'detail.showCollapseIcon')) {
            $rules = (array) data_get($row, '__turbine_rules', []);

            /** @var view-string $toggleDetailView */
            $toggleDetailView = data_get(collect($rules)->last(), 'toggleDetailView')
                ?? theme_view('toggle-detail');

            $html .= view($toggleDetailView, [
                'setUp' => $this->component->setUp,
                'tableName' => $this->component->tableName,
                'rowId' => $rowId,
                'view' => data_get($this->component->setUp, 'detail.viewIcon') ?? null,
            ])->render();
        }

        if ($this->component->radio && $this->component->radioAttribute) {
            /** @var view-string $radioView */
            $radioView = theme_view('table.radio-row');

            $html .= view($radioView, [
                'row' => $row,
                'attribute' => $row->{$this->component->radioAttribute} ?? null,
            ])->render();
        }

        if ($this->component->checkbox && $this->component->checkboxAttribute) {
            $html .= $this->renderCheckboxCell($row);
        }

        return $html.$this->component->renderCells($row, $rowIndex, $childIndex, $parentId, $rowId);
    }

    public function renderExpandRow(string|int $rowId): string
    {
        $id = e((string) $rowId);

        return '<tr x-cloak expand data-expand-for="'.$id.'" wire:key="expand-'.$id.'"'
            .' x-show="isExpanded(\''.$id.'\')" x-transition'
            .' class="'.theme('table.body.tr.responsive').'">'
            .'<td colspan="999"><div class="flex gap-x-6 gap-y-2 flex-wrap p-2 responsive-row-expand-container"></div></td>'
            .'</tr>';
    }

    private function renderResponsiveToggle(string|int $rowId): string
    {
        $id = e((string) $rowId);
        $iconClass = theme('table.body.tr.responsive_toggle_icon');

        return '<td x-cloak x-show="hasHiddenElements" class="w-0 '.theme('table.layout.td').'">'
            .'<button class="flex items-center" x-on:click="toggleExpanded(\''.$id.'\')">'
            .'<svg class="'.e($iconClass).' w-5 h-5 transition-all duration-300" fill="none" viewBox="0 0 24 24"'
            .' stroke="currentColor" stroke-width="2" x-bind:class="rotateClass(\''.$id.'\')">'
            .'<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />'
            .'</svg>'
            .'</button></td>';
    }

    private function renderCheckboxCell(object $row): string
    {
        $checkboxAttribute = $row->{$this->component->checkboxAttribute} ?? null;

        $inputAttributes = new ComponentAttributeBag([
            'class' => theme('table.checkbox.input'),
        ]);

        /** @var array<int, array<string, mixed>> $allRules */
        $allRules = (array) data_get($row, '__turbine_rules', []);

        $rules = collect($allRules)
            ->where('apply', true)
            ->where('forAction', RuleManager::TYPE_CHECKBOX)
            ->last();

        $ruleAttributes = data_get($rules, 'attributes');

        if (is_array($ruleAttributes)) {
            foreach ($ruleAttributes as $key => $value) {
                $inputAttributes = $inputAttributes->merge([$key => $value]);
            }
        }

        $disable = (bool) data_get($rules, 'disable');
        $hide = (bool) data_get($rules, 'hide');

        if ($hide) {
            return '<td wire:key="checkbox-row-hide-'.e($checkboxAttribute).'" class="'.theme('table.checkbox.th').'"></td>';
        }

        if ($disable) {
            return '<td wire:key="checkbox-row-disable-'.e($checkboxAttribute).'" class="'.theme('table.checkbox.th').'">'
                .'<div class="'.theme('table.checkbox.base').'">'
                .'<label class="'.theme('table.checkbox.label').'">'
                .'<input '.$inputAttributes.' disabled type="checkbox">'
                .'</label>'
                .'</div>'
                .'</td>';
        }

        return '<td wire:key="checkbox-row-'.e($checkboxAttribute).'" class="'.theme('table.checkbox.th').'">'
            .'<div class="'.theme('table.checkbox.base').'">'
            .'<label class="'.theme('table.checkbox.label').'">'
            .'<input'
            .' x-data'
            .' type="checkbox"'
            .' '.$inputAttributes
            .' x-on:click="$store.pgBulkActions.add($event.target.value, \''.e($this->component->tableName).'\')"'
            .' wire:model="checkboxValues"'
            .' value="'.e($checkboxAttribute).'"'
            .'>'
            .'</label>'
            .'</div>'
            .'</td>';
    }
}
