<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use Illuminate\Contracts\Support\{Htmlable, Renderable};
use Illuminate\Support\Arr;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final readonly class CellRenderer
{
    public function __construct(private PowerGridComponent $component) {}

    public function render(object $row, int $rowIndex, ?int $childIndex, mixed $parentId, string|int $rowId): string
    {
        $actionsWrapperClass = theme('table.body.td.actions_wrapper');
        $hasActionsFromView = method_exists($this->component, 'actionsFromView');

        $childKey = (string) ($childIndex ?? 0);
        $rowKey = e((string) $rowId);

        $html = '';

        foreach ($this->component->columnViewModels as $column) {
            $html .= '<td class="'.$column->tdClass.'" style="'.$column->tdStyle.'"'
                .' wire:key="row-'.$rowKey.'-'.e($column->dataField).'-'.e($childKey).'"'
                .' data-column="'.e($column->isAction ? 'actions' : $column->dataField).'">'
                .$this->renderCellBody(
                    $column,
                    $row,
                    $rowIndex,
                    $parentId,
                    trim($actionsWrapperClass.' '.$column->alignClasses),
                    $hasActionsFromView,
                )
                .'</td>';
        }

        return $html;
    }

    private function renderCellBody(
        ColumnViewModel $column,
        object $row,
        int $rowIndex,
        mixed $parentId,
        string $actionsWrapperClass,
        bool $hasActionsFromView,
    ): string {
        if ($column->hasCustomContent && $column->customView !== null) {
            /** @var view-string $view */
            $view = $column->customView;

            return view($view, $column->customParams)->render();
        }

        if ($column->isAction) {
            return $this->renderActionCell($row, $actionsWrapperClass, $hasActionsFromView);
        }

        $pluginContent = $this->component->renderColumnContent($column->column, $row);

        if ($pluginContent !== null) {
            return $pluginContent;
        }

        return $this->renderContentCell($column, $row, $rowIndex, $parentId);
    }

    private function renderActionCell(object $row, string $actionsWrapperClass, bool $hasActionsFromView): string
    {
        $html = '<div class="pg-actions">';

        if ($hasActionsFromView) {
            $rendered = $this->toHtmlString($this->component->actionsFromView($row));

            if ($rendered !== '') {
                $pk = data_get($row, $this->component->realPrimaryKey);
                $html .= '<div wire:key="actions-view-'.e(is_scalar($pk) ? (string) $pk : '').'">'.$rendered.'</div>';
            }
        }

        return $html.'<div wire:replace.self><div class="'.$actionsWrapperClass.'">'
            .$this->component->renderActions($row).'</div></div></div>';
    }

    private function renderContentCell(ColumnViewModel $column, object $row, int $rowIndex, mixed $parentId): string
    {
        $rawContent = $row->{$column->field} ?? '';
        $templateContent = null;

        if (is_array($rawContent)) {
            $templateContent = $rawContent;
            $rawContent = '';
        }

        if ($rawContent instanceof \UnitEnum) {
            $rawContent = $rawContent instanceof \BackedEnum ? $rawContent->value : $rawContent->name;
        }

        $content = match (true) {
            $rawContent instanceof Htmlable => $rawContent->toHtml(),
            is_scalar($rawContent) || $rawContent instanceof \Stringable => e((string) $rawContent),
            default => '',
        };

        $spanClass = $column->spanClassStatic ?? Arr::toCssClasses([
            $column->contentClassField,
            $this->contentClassFor($column, $content),
        ]);

        $inner = filled($templateContent)
            ? '<div x-data="pgRenderRowTemplate" data-pg-params="'
                .e((string) json_encode(['parentId' => $parentId, 'templateContent' => $templateContent]))
                .'" x-html="rendered"></div>'
            : '<div>'.($column->index ? $rowIndex : $content).'</div>';

        return '<span class="'.$spanClass.'">'.$inner.'</span>';
    }

    private function toHtmlString(mixed $value): string
    {
        return match (true) {
            is_string($value) => $value,
            $value instanceof Renderable => $value->render(),
            $value instanceof \Stringable => (string) $value,
            default => '',
        };
    }

    private function contentClassFor(ColumnViewModel $column, string $content): string
    {
        if (! is_array($column->contentClasses) || ! array_key_exists($content, $column->contentClasses)) {
            return '';
        }

        $class = $column->contentClasses[$content];

        return is_scalar($class) ? (string) $class : '';
    }
}
