<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Editable;

use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use stdClass;

class EditablePlugin extends PluginBase
{
    public function name(): string
    {
        return 'editable';
    }

    public function isEnabled(): bool
    {
        return collect($this->component->columns)
            ->contains(fn ($column) => ! empty(data_get($column, 'editable')));
    }

    public function handles(Column|array $column): bool
    {
        return ! empty(data_get($column, 'editable'));
    }

    public function render(Column|array $column, mixed $row): ?string
    {
        if ($this->shouldShowEditOnClick($column, $row)) {
            return view('powergrid-plugins::Editable.index', [
                'tableName' => $this->component->tableName,
                'primaryKey' => $this->component->realPrimaryKey,
                'row' => $row,
                'field' => data_get($column, 'dataField', data_get($column, 'field')),
                'currentTable' => $this->component->currentTable,
                'showErrorBag' => config('livewire-powergrid.show_error_bag'),
                'editable' => data_get($column, 'editable'),
                'js' => file_get_contents(__DIR__.'/index.js'),
                'css' => file_get_contents(__DIR__.'/index.css'),
            ])->render();
        }

        return null;
    }

    #[On('pg:editable-{tableName}')]
    public function inputTextChanged(...$params): void
    {
        // Livewire passes named parameters as positional args in declaration order
        // Expected: field, id, value
        [$field, $id, $value] = $params;

        data_set($this->component, "$field.{$id}", $value);

        $this->component->onUpdatedEditable($id, $field, $value);

        $this->component->dispatch('pg:editable-close-'.$id);
    }

    private function shouldShowEditOnClick(stdClass|Column|array $column, mixed $row): bool
    {
        $hasPermission = boolval(data_get($column, 'editable.hasPermission', false));

        $editOnClickVisibility = data_get(
            collect((array) data_get($row, '__powergrid_rules'))
                ->where('apply', true)
                ->last(),
            'editOnClickVisibility'
        );

        if ($editOnClickVisibility === 'hide') {
            return false;
        }

        if ($editOnClickVisibility === 'show') {
            return true;
        }

        return $hasPermission;
    }
}
