<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Livewire\Attributes\On;

trait SoftDeletes
{
    public string $softDeletes = '';

    #[On('pg:softDeletes-{tableName}')]
    public function softDeletes(string $softDeletes): void
    {
        $this->softDeletes = $softDeletes;
    }
}
