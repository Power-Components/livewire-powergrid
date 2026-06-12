<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Traits;

trait WithSearch
{
    public string $search = '';

    public function updatedSearch(): void
    {
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }
}
