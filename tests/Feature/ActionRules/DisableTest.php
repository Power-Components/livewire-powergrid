<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;

it('add rule \'disable\' when dishId === 9', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('testActions', [
            Button::add('edit')
                ->caption('Edit')
                ->class('text-center')
                ->openModal('modal-edit', ['dishId' => 'id']),
        ])
        ->set('testActionRules', [
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id === 9)
                ->disable(),
        ])
        ->set('search', 'Polpetone Filé Mignon')
        ->assertSeeHtml([
            'disabled="disabled"',
            'class="text-center"',
        ])
        ->set('search', 'Peixada da chef Nábia')
        ->assertDontSeeHtml('disabled="disabled"')
        ->assertSeeHtml('class="text-center"');
})->with('disable')->group('actionRules');

dataset('disable', [
    'tailwind'       => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
