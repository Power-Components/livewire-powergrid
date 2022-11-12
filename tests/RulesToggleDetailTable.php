<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

class RulesToggleDetailTable extends DishTableBase
{
    public array $setUpTest = [];

    public function setUp(): array
    {
        $this->showCheckBox();

        return $this->setUpTest;
    }
}
