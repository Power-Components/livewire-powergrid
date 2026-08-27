<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

trait Detail
{
    /** @var array<string, true> */
    public array $openedDetailIds = [];

    public function isDetailOpen(int|string $rowId): bool
    {
        return isset($this->openedDetailIds[(string) $rowId]);
    }

    public function toggleDetail(string $rowId): void
    {
        if (! isset($this->setUp['detail'])) {
            return;
        }

        $key = (string) $rowId;

        if (isset($this->openedDetailIds[$key])) {
            unset($this->openedDetailIds[$key]);
        } elseif ((bool) data_get($this->setUp, 'detail.singleExpand', false)) {
            $this->openedDetailIds = [$key => true];
        } else {
            $this->openedDetailIds[$key] = true;
        }

        $this->renderGridPartials();
    }

    /**
     * @return array{view: string|null, options: mixed}
     */
    public function detailForRow(mixed $row): array
    {
        $rules = data_get($row, '__turbine_rules', []);
        $rules = is_array($rules) ? $rules : [];

        $withView = collect($rules)
            ->where('apply', true)
            ->last(fn ($rule) => filled(data_get($rule, 'detailView')));

        $view = data_get($withView, 'detailView');
        $options = data_get($withView, 'options', []);

        if (is_array($view)) {
            $options = data_get($view, 'options', $options);
            $view = data_get($view, 'detailView');
        }

        if (! is_string($view) || $view === '') {
            $view = data_get($this->setUp, 'detail.view');
            $view = is_string($view) ? $view : null;
            $options = data_get($this->setUp, 'detail.options', []);
        }

        return [
            'view' => $view,
            'options' => $options,
        ];
    }
}
