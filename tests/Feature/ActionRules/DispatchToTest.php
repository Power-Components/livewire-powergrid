<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Facades\Rule;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{Concerns\Components\DishTableBase, Concerns\Models\Dish};

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
                ->dispatchTo('dishes-table', 'deletedEvent', ['dishId' => 5]),
        ];
    }
};

it('add rule \'dispatchTo\' when dishId == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        //page 1
        ->set('setUp.footer.perPage', 5)
        ->assertSeeHtml('$dispatchTo(&#039;dishes-table&#039;,deletedEvent&#039;, JSON.parse(&#039;{\u0022dishId\u0022:5}&#039;))')
        ->assertDontSee('$dispatchTo(&#039;dishes-table&#039;,deletedEvent&#039;, JSON.parse(&#039;{\u0022dishId\u0022:4}&#039;))');
})->with('emit_to_themes_with_join')->group('actionRules');

dataset('emit_to_themes_with_join', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
