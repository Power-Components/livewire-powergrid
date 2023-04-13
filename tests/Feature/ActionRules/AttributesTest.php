<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;

it('set custom class when dish-id == 1 on button', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('testActions', [
            Button::add('edit')
                ->caption('Edit')
                ->class('text-center'),
        ])
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtml([
            'class="text-center"',
        ]);
})->with('attributes_themes_with_join')->group('actionRules');

it('change class to bg-pg-primary-100 when dish-id == 1', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('testActions', [
            Button::add('edit')
                ->caption('Edit')
                ->class('text-center'),
        ])
        ->set('testActionRules', [
            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id == 1)
                ->setAttribute('class', 'bg-pg-primary-400'),
        ])
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtmlInOrder([
            '<tr',
            'class="border border-pg-primary-100 dark:border-pg-primary-400 hover:bg-pg-primary-50 dark:bg-pg-primary-700 dark:odd:bg-pg-primary-800 dark:odd:hover:bg-pg-primary-900 dark:hover:bg-pg-primary-700 bg-pg-primary-400"',
        ])
        ->set('search', 'Barco-Sushi da Sueli')
        ->assertDontSeeHtml(
            'class="border border-pg-primary-100 dark:border-pg-primary-400 hover:bg-pg-primary-50 dark:bg-pg-primary-700 dark:odd:bg-pg-primary-800 dark:odd:hover:bg-pg-primary-900 dark:hover:bg-pg-primary-700 bg-pg-primary-400"',
        );
})->with('attributes_tailwind_with_join')->group('actionRules');

it('see many custom \'attributes\' when dish-id == 2', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Peixada da chef Nábia')
        ->set('testActions', [
            Button::add('edit')
                ->caption('Edit')
                ->class('text-center'),
        ])
        ->set('testActionRules', [
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id == 2)
                ->setAttribute('class', 'bg-pg-primary-200')
                ->setAttribute('title', 'Title changed by setAttributes when id 2')
                ->setAttribute('wire:click', ['test', ['param1' => 2, 'dishId' => 'id']]),
        ])
        ->assertSeeHtmlInOrder([
            '<button class="text-center bg-pg-primary-200"',
            'wire:click="test(' . e('{"param1":2,"dishId":2}') . ')"',
            'title="Title changed by setAttributes when id 2"',
            'Edit',
        ]);
})->with('attributes_themes_with_join')->group('actionRules');

it('see many \'attributes\' when dish-id == 5', function (string $component, object $params) {
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
                ->setAttribute('class', 'bg-pg-primary-500')
                ->setAttribute('title', 'Title changed by setAttributes when id 5')
                ->setAttribute('wire:click', ['test', ['param1' => 5, 'dishId' => 'id']]),
        ])
        ->set('search', 'Francesinha vegana')
        ->assertSeeHtmlInOrder([
            '<button class="text-center bg-pg-primary-500"',
            'wire:click="test(' . e('{"param1":5,"dishId":5}') . ')"',
            'title="Title changed by setAttributes when id 5"',
        ]);
})->with('attributes_themes_with_join')->group('actionRules');

it('check if there is no rule when dish-id == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('testActions', [
            Button::add('edit')
                ->caption('Edit')
                ->class('text-center'),
        ])
        ->set('search', 'Barco-Sushi da Sueli')
        ->assertDontSeeHtml([
            '<button class="text-center bg-pg-primary-500"',
            'wire:click="test(' . e('{"param1":5,"dishId":5}') . ')"',
            'title="Title changed by setAttributes when id 5"',
        ]);
})->with('attributes_themes_with_join')->group('actionRules');

dataset('attributes_themes_with_join', [
    'tailwind'       => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

dataset('attributes_tailwind_with_join', [
    'tailwind'      => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'tailwind join' => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => true]],
]);
