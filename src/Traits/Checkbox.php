<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Exception;

trait Checkbox
{
    public bool $checkbox = false;

    public bool $checkboxAll = false;

    public array $checkboxValues = [];

    public string $checkboxAttribute = '';

    /**
     * @throws Exception
     */
    public function selectCheckboxAll()
    {
        if (!$this->checkboxAll) {
            $this->checkboxValues = [];

            return;
        }

        collect($this->fillData()->items())->each(function ($model) {
            $this->checkboxValues[] = (string)$model->{$this->checkboxAttribute};
        });
    }
}
