<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Livewire\Attributes\{Locked, On};

trait SoftDeletes
{
    #[Locked]
    public string $softDeletes = '';

    #[On('pg:softDeletes-{tableName}')]
    public function softDeletes(string $softDeletes): void
    {
        $this->softDeletes = in_array($softDeletes, ['withTrashed', 'onlyTrashed'], true)
            ? $softDeletes
            : '';
    }
}
