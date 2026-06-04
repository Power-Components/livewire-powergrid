<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

trait ManageRow
{
    public function prepareRowTemplates(): void
    {
        $rowTemplates = json_encode($this->rowTemplates());

        $this->dispatch('pgRowTemplates', $rowTemplates);
    }
}
