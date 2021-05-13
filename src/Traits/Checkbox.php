<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

trait Checkbox
{
    public bool $checkbox = false;

    public bool $checkboxAll = false;

    public array $checkboxValues = [];

    public string $checkboxAttribute;

    public function updatedCheckboxAll()
    {
        $this->checkboxValues = [];

        if ($this->checkboxAll) {
            $this->fromCollection()->each(function($model) {
                $this->checkboxValues[] = (string)$model->{$this->checkboxAttribute};
            });
        }
    }
}
