<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\{DishTableBase, RulesBladeComponentTable};

it('add rule \'bladeComponent\' when dish-id == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('testActions', [
            Button::add('edit')
                ->caption('Edit')
                ->class('text-center'),
        ])
        ->set('testActionRules', [
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id == 5)
                ->bladeComponent('livewire-powergrid::icons.arrow', ['dish-id' => 'id']),
        ])
        ->set('setUp.footer.perPage', 10)
        ->assertSeeHtmlInOrder([
            '<svg dish-id="5"',
            '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />',
        ])
        ->assertDontSeeHtml('<svg dish-id="1"')
        ->set('search', 'Polpetone Filé Mignon')
        ->assertDontSeeHtml([
            '<svg dish-id="9"',
            '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />',
        ]);
})->with('bladeComponent')->group('actionRules');

dataset('bladeComponent', [
    'tailwind'       => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
