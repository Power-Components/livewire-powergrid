<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

class RulesToggleDetailTable extends DishTableBase
{
    public array $setUpTest = [];

    public function setUp(): array
    {
        config()->set('livewire.inject_morph_markers', false);

        $this->showCheckBox();

        return $this->setUpTest;
    }
}
