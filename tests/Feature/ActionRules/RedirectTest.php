<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('edit')
                ->slot('Edit')
                ->class('text-center')
                ->openModal('modal-edit', ['dishId' => $row->id]),
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id === 1)
                ->redirect(fn (Dish $dish) => 'https://www.dish.test/sorry-out-of-stock?dish=' . $dish->id),
        ];
    }
};

it('add rule \'redirect\' when out of stock and dishId === 1', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtmlInOrder(['a', 'href="https://www.dish.test/sorry-out-of-stock?dish=1'])
        ->assertDontSeeHtml('href="https://www.dish.test/sorry-out-of-stock?dish=2');
})->with('redirect')
    ->group('actionRules');

dataset('redirect', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
