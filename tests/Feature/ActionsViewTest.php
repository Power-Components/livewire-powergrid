<?php

use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Providers\SupportLivewireVersions;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\{DishesActionTable, DishesTable};
use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, DaisyUI, Tailwind};

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class() extends DishesTable
{
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
        ->call('setTestThemeClass', $params->theme)
        ->assertSeeInOrder([
            'Dish From Actions View: 1',
            'Dish From Actions View: 2',
            'Dish From Actions View: 3',
            'Dish From Actions View: 4',
            'Dish From Actions View: 5',
            'Dish From Actions View: 6',
        ]);
})->with([
    'tailwind' => [$component::class, (object) ['theme' => Tailwind::class, 'field' => 'name']],
    'bootstrap' => [$component::class, (object) ['theme' => Bootstrap5::class, 'field' => 'name']],
    'daisyui' => [$component::class, (object) ['theme' => DaisyUI::class, 'field' => 'name']],
]);

it('scopes the actions-updated event to the grid instance', function () {
    if (! SupportLivewireVersions::isV4()) {
        $this->markTestSkipped('Instance-scoped actions event only applies to Livewire v4.');
    }

    $html = html_entity_decode(livewire(DishesActionTable::class)->html());

    expect($html)
        ->toContain("new CustomEvent('pg:actions-updated', { detail: { id: \$wire.id } })")
        ->not->toContain("new CustomEvent('pg:actions-updated')");
});

it('keeps the actions event scoped per instance with multiple grids on the page', function () {
    if (! SupportLivewireVersions::isV4()) {
        $this->markTestSkipped('Instance-scoped actions event only applies to Livewire v4.');
    }

    $first  = livewire(DishesActionTable::class);
    $second = livewire(DishesActionTable::class);

    expect($first->id())->not->toBe($second->id());

    foreach ([$first, $second] as $grid) {
        expect(html_entity_decode($grid->html()))
            ->toContain("new CustomEvent('pg:actions-updated', { detail: { id: \$wire.id } })");
    }
});
