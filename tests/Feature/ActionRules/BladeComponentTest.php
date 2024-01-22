<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public int $dishId;

    public function actions(Dish $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->class('text-center'),
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id == 5)
                ->bladeComponent('livewire-powergrid::icons.arrow', [
                    'dish-id' => 'id',
                    'class'   => 'w-5 h-5',
                ]),
        ];
    }
};

it('add rule \'bladeComponent\' when dish-id == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 10)
        ->assertSeeHtmlInOrder([<<<HTML
<svg
    class="w-5 h-5" dish-id="id"
    fill="none"
    viewBox="0 0 24 24"
    stroke="currentColor"
    stroke-width="2"
>
    <path
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M9 5l7 7-7 7"
    />
</svg>
HTML])
        ->assertDontSeeHtml('<svg dish-id="1"')
        ->set('search', 'Polpetone Filé Mignon')
        ->assertDontSeeHtml([
            '<svg dish-id="9"',
        ]);
})
    ->with(
        [
            'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
            'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
            'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
            'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
        ]
    )->group('actionRules');
