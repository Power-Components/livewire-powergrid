<?php

use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\{Button, Detail, Footer, Tests\Concerns\Components\DishTableBase};

$component = new class () extends DishTableBase {
    public function setUp(): array
    {
        config()->set('livewire.inject_morph_markers', false);

        return [
            Footer::make()
                ->showPerPage(5),

            Detail::make()
                ->view('livewire-powergrid::tests.detail')
                ->params([
                    'name' => 'Luan',
                ])
                ->showCollapseIcon(),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::make('toggleDetail', 'Toggle Detail')
                ->class('text-center')
                ->toggleDetail(),
        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id == 3)
                ->detailView('livewire-powergrid::tests.detail-rules', ['newParameter' => 1]),
        ];
    }
};

it('change \'detailRow\' component when dish-id == 1', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->assertDontSeeHtml([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ])
        // show detail row #1
        ->call('toggleDetail', 1)
        ->assertSeeHtmlInOrder([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => true,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ])
        ->assertSet('setUp.detail.state', [
            1 => true,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ])
        ->call('toggleDetail', 1)
        // show detail row #1
        ->call('toggleDetail', 3)
        ->assertSeeHtmlInOrder([
            '<div>Id 3</div>',
            '<div>Options {"name":"Luan","newParameter":1}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => false,
            2 => false,
            3 => true,
            4 => false,
            5 => false,
        ]);
})->skip()->with('detail_row_tailwind_join')
    ->group('actionRules');

dataset('detail_row_tailwind_join', [
    'tailwind'      => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'tailwind join' => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
]);
