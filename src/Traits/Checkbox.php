<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

trait Checkbox
{
    public bool $checkbox = false;

    public bool $checkboxAll = false;

    public array $checkboxValues = [];

    public string $checkboxAttribute;

    /**
     * @throws \Exception
     */
    public function selectCheckboxAll()
    {
        if (!$this->checkboxAll) {
            $this->checkboxValues = [];
            return;
        }

        collect($this->loadData()->items())->each(function ($model) {
            $this->checkboxValues[] = (string)$model->{$this->checkboxAttribute};
        });
    }
}


