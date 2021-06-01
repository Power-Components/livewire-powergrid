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
        $this->checkboxValues = [];

        if (!$this->checkboxAll) {
            return;
        }
        if ($this->isCollection) {
            $this->resolveCollection()->each(function ($model) {
                $this->checkboxValues[] = (string)$model->{$this->checkboxAttribute};
            });

            return;
        }
        $this->resolveModel()->each(function ($model) {
            $this->checkboxValues[] = (string)$model->{$this->checkboxAttribute};
        });
    }
}
