<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

;

todo('add rule \'hide\' when dishId == 2', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('testActions', [
            Button::add('edit')
                ->slot('<div id="edit">Edit</div>')
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
})->with('hide_themes_with_join')->group('actionRules');

dataset('hide_themes_with_join', [
    'tailwind'       => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
