<?php

use PowerComponents\LivewirePowerGrid\Column;

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishesTable {
    public function columns(): array
    {
        return [
            Column::add()
                ->title('Id')
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Dish')
                ->field('name')
                ->searchable()
                ->contentClasses('bg-custom-500 text-custom-500')
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function actionsFromView($row)
    {
        return view('livewire-powergrid::tests.actions-view', compact('row'));
    }
};

it('can render actionsFromView property', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeInOrder([
            'Dish From Actions View: 1',
            'Dish From Actions View: 2',
            'Dish From Actions View: 3',
            'Dish From Actions View: 4',
            'Dish From Actions View: 5',
            'Dish From Actions View: 6',
        ]);
})->with([
    'tailwind'  => [$component::class, (object) ['theme' => 'tailwind', 'field' => 'name']],
    'bootstrap' => [$component::class, (object) ['theme' => 'bootstrap', 'field' => 'name']],
]);
