<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Livewire\Attributes\Locked;

trait Radio
{
    public bool $radio = false;

    #[Locked]
    public string $radioAttribute = 'id';

    public string $selectedRow = '';

    public function showRadioButton(string $attribute = 'id'): self
    {
        $this->radio = true;
        $this->radioAttribute = $attribute;

        return $this;
    }
}
