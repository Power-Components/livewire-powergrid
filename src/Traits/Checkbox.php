<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Exception;
use Illuminate\Pagination\AbstractPaginator;

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
        $data = $this->fillData();

        collect($data->items())->each(function ($model) {
            $this->checkboxValues[] = (string) $model->{$this->checkboxAttribute};
        });
    }
}
