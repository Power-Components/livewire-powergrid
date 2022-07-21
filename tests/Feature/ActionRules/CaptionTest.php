<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\{DishTableBase, RulesCaptionTable};

it('add rule \'caption\' when dish out of stock', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('testActions', [
            Button::add('edit')
                ->caption('<div id="edit">Edit</div>')
                ->class('text-center'),
        ])
        ->set('testActionRules', [
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id == 4)
                ->caption('Cation Edit for id 4'),
        ])
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtml('<div id="edit">Edit</div>')
        ->assertDontSeeHtml('Cation Edit for id 4')
        ->set('search', 'Bife à Rolê')
        ->assertDontSeeHtml('<div id="edit">Edit</div>')
        ->assertSeeHtml('Cation Edit for id 4');
})->with('caption')->group('actionRules');

dataset('caption', [
    'tailwind'       => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
