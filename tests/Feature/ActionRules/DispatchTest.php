<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\{livewire};

$component = new class () extends DishTableBase {
    public int $dishId;

    public function actions($row): array
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
                ->dispatch('toggleEvent', ['dishId' => 5]),
        ];
    }
};

it('add rule \'dispatch\' when dishId == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->assertDontSeeHtml("\$dispatch(&#039;toggleEvent&#039;, JSON.parse(&#039;{\\u0022dishId\\u0022:1}&#039;))")
        ->set('search', 'Francesinha vegana')
        ->assertSeeHtml("\$dispatch(&#039;toggleEvent&#039;, JSON.parse(&#039;{\\u0022dishId\\u0022:5}&#039;))");
})->with([
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
])->group('actionRules');
