<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

trait Radio
{
    public bool $radio = false;

    public string $radioAttribute = 'id';

    public string $selectedRow = '';

    public function showRadioButton(string $attribute = 'id'): self
    {
        $this->radio          = true;
        $this->radioAttribute = $attribute;

        return $this;
    }
}
