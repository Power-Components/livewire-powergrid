<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Exception;
use Livewire\Attributes\On;

trait HandlesFilterInputs
{
    /**
     * @param  list<string>  $values
     *
     * @throws Exception
     */
    #[On('pg:multiSelect-{tableName}')]
    public function multiSelectChanged(
        string $field,
        string $label,
        array $values,
    ): void {
        $this->resetPage();

        $this->setInFilters($this->filters, "multi_select.$field", $values);

        $this->addEnabledFilters($field, $label);

        if (count($values) === 0) {
            $this->clearFilter($field);
        }

        $this->afterChangedMultiSelectFilter($field, $values);

        $this->persistState('filters');

        $this->renderFilterPanelPartial();
    }

    /**
     * @throws Exception
     */
    public function filterSelect(string $field, string $label): void
    {
        $this->resetPage();

        $this->addEnabledFilters($field, $label);

        $value = data_get($this->filters, "select.$field");

        if (blank($value)) {
            $this->clearFilter($field);
        }

        $this->afterChangedSelectFilter($field, $label, $value);

        $this->persistState('filters');

        $this->renderFilterPanelPartial();
    }

    /**
     * @param  array<string, mixed>  $params
     *
     * @throws Exception
     */
    public function filterNumberStart(string $field, array $params, string $value): void
    {
        /** @var string $title */
        $title = data_get($params, 'title');

        $this->resetPage();

        $this->addEnabledFilters($field, $title);

        if (blank($value)) {
            $this->clearFilter($field);
        }

        $this->afterChangedNumberStartFilter($field, $title, $value);

        $this->persistState('filters');

        $this->renderFilterPanelPartial();
    }

    /**
     * @param  array<string, mixed>  $params
     *
     * @throws Exception
     */
    public function filterNumberEnd(string $field, array $params, string $value): void
    {
        /** @var string $title */
        $title = data_get($params, 'title');

        $this->resetPage();

        $this->addEnabledFilters($field, $title);

        if (blank($value)) {
            $this->clearFilter($field);
        }

        $this->afterChangedNumberEndFilter($field, $title, $value);

        $this->persistState('filters');

        $this->renderFilterPanelPartial();
    }

    /**
     * @throws Exception
     */
    public function filterBoolean(string $field, string $value, string $label): void
    {
        $this->resetPage();

        $this->addEnabledFilters($field, $label);

        if ($value == 'all') {
            $this->clearFilter($field);
        }

        $this->afterChangedBooleanFilter($field, $label, $value);

        $this->persistState('filters');

        $this->renderFilterPanelPartial();
    }

    /**
     * @throws Exception
     */
    public function filterInputText(string $field, string $value, string $label = ''): void
    {
        $this->resetPage();

        $this->addEnabledFilters($field, $label);

        if (blank($value)) {
            $this->clearFilter($field);
        }

        $this->afterChangedInputTextFilter($field, $label, $value);

        $this->persistState('filters');

        $this->renderFilterPanelPartial();
    }

    /**
     * @throws Exception
     */
    public function filterInputTextOptions(string $field, string $value, string $label = ''): void
    {
        if (! $this->isDeclaredFilterField($field)) {
            return;
        }

        $this->setInFilters($this->filters, 'input_text_options.'.$field, $value);

        $disabled = false;

        $this->resetPage();

        if (in_array($value, ['is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank'])) {
            $disabled = true;

            if (str($field)->contains('.')) {
                $this->setInFilters($this->filters, 'input_text.'.str($field)->before('.').'.'.str($field)->after('.'), null);
            } else {
                $this->setInFilters($this->filters, 'input_text.'.$field, null);
            }
        }

        if (! collect($this->enabledFilters)->where('field', $field)->count()) {
            $this->enabledFilters[] = [
                'field' => $field,
                'label' => $label,
                'disabled' => $disabled,
            ];
        }

        if (blank($value)) {
            $this->clearFilter($field);
        }
        $this->persistState('filters');

        $this->renderFilterPanelPartial();
    }
}
