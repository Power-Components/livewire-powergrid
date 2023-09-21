<?php

use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\{Footer, Header};

$component = new class () extends DishTableBase {
    public function setUp(): array
    {
        $this->showRadioButton();

        return [
            Header::make()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }
};

todo('selectedRow works properly', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('selectedRow', 2);
})->with([
    'tailwind -> id'  => [$component::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id' => [$component::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
]);
