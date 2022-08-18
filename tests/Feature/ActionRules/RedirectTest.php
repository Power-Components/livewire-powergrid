<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\{DishTableBase, RulesRedirectTable};

it('add rule \'redirect\' when out of stock and dishId !== 8', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testActions', [
            Button::add('edit')
                ->caption('<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('modal-edit', ['dishId' => 'id']),
        ])
        ->set('testActionRules', [
            Rule::button('edit')
                ->when(fn (Dish $dish) => (bool) $dish->in_stock === false)
                ->redirect(fn (Dish $dish) => 'https://www.dish.test/sorry-out-of-stock?dish=' . $dish->id),
        ])
        ->set('setUp.footer.perPage', 10)
        ->assertSeeHtml(['href="https://www.dish.test/sorry-out-of-stock?dish=6', 'target="_blank"'])
        ->assertSeeHtml(['href="https://www.dish.test/sorry-out-of-stock?dish=7', 'target="_blank"'])
        ->assertSeeHtml('wire:click="$emit(&quot;openModal&quot;, &quot;modal-edit&quot;, {&quot;dishId&quot;:5})"')
        ->assertSeeHtml(['href="https://www.dish.test/sorry-out-of-stock?dish=8', 'target="_blank"'])
        ->assertSeeHtml(['href="https://www.dish.test/sorry-out-of-stock?dish=10', 'target="_blank"']);
})->with('redirect')->group('actionRules');

dataset('redirect', [
    'tailwind'       => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
