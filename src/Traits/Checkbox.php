<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Exception;
use Illuminate\Pagination\AbstractPaginator;
use PowerComponents\LivewirePowerGrid\Helpers\Helpers;

trait Checkbox
{
    public bool $checkbox = false;

    public bool $checkboxAll = false;

    public array $checkboxValues = [];

    public string $checkboxAttribute = '';

    /**
     * @throws Exception
     */
    public function selectCheckboxAll(): void
    {
        if (!$this->checkboxAll) {
            $this->checkboxValues = [];

            return;
        }

        /** @var AbstractPaginator $data */
        $data      = $this->fillData();

        $helperClass = resolve(Helpers::class);

        collect($data->items())->each(function ($model) use ($helperClass) {
            $rules    = $helperClass->makeActionRules('pg:checkbox', $model);
            if (isset($rules['hide'])) {
                return;
            }
            $value    = $model->{$this->checkboxAttribute};
            if (!in_array($value, $this->checkboxValues)) {
                $this->checkboxValues[] = (string) $model->{$this->checkboxAttribute};
            }
        });
    }
}
