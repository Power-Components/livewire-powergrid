<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\{DishTableBase, RulesHideTable};

it('add rule \'hide\' when dishId == 2', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('testActions', [
            Button::add('edit')
                ->caption('<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('modal-edit', ['dishId' => 'id']),
        ])
        ->set('testActionRules', [
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id == 2)
                ->hide(),
        ])
        ->assertSeeHtml('wire:click="$emit(&quot;openModal&quot;, &quot;modal-edit&quot;, {&quot;dishId&quot;:1})"')
        ->assertDontSeeHtml('wire:click="$emit(&quot;openModal&quot;, &quot;modal-edit&quot;, {&quot;dishId&quot;:2})"');
})->with('hide')->group('actionRules');

dataset('hide', [
    'tailwind'       => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
