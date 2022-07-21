<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\{DishTableBase, RulesAttributesTable};

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
})->with('buttonAttributes')->group('actionRules');

it('change class to bg-slate-100 when dish-id == 1', function (string $component, object $params) {
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
                ->setAttribute('class', 'bg-slate-400'),
        ])
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtmlInOrder([
            '<tr',
            'class="border border-slate-100 dark:border-slate-400 hover:bg-slate-50 dark:bg-slate-700 dark:odd:bg-slate-800 dark:odd:hover:bg-slate-900 dark:hover:bg-slate-700 bg-slate-400"',
        ])
        ->set('search', 'Barco-Sushi da Sueli')
        ->assertDontSeeHtml(
            'class="border border-slate-100 dark:border-slate-400 hover:bg-slate-50 dark:bg-slate-700 dark:odd:bg-slate-800 dark:odd:hover:bg-slate-900 dark:hover:bg-slate-700 bg-slate-400"',
        );
})->with('buttonAttributesTailwind')->group('actionRules');

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
                ->setAttribute('class', 'bg-slate-200')
                ->setAttribute('title', 'Title changed by setAttributes when id 2')
                ->setAttribute('wire:click', ['test', ['param1' => 2, 'dishId' => 'id']]),
        ])
        ->assertSeeHtmlInOrder([
            '<button class="text-center bg-slate-200"',
            'wire:click="test(' . e('{"param1":2,"dishId":2}') . ')"',
            'title="Title changed by setAttributes when id 2"',
            'Edit',
        ]);
})->with('buttonAttributes')->group('actionRules');

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
                ->setAttribute('class', 'bg-slate-500')
                ->setAttribute('title', 'Title changed by setAttributes when id 5')
                ->setAttribute('wire:click', ['test', ['param1' => 5, 'dishId' => 'id']]),
        ])
        ->set('search', 'Francesinha vegana')
        ->assertSeeHtmlInOrder([
            '<button class="text-center bg-slate-500"',
            'wire:click="test(' . e('{"param1":5,"dishId":5}') . ')"',
            'title="Title changed by setAttributes when id 5"',
        ]);
})->with('buttonAttributes')->group('actionRules');

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
            '<button class="text-center bg-slate-500"',
            'wire:click="test(' . e('{"param1":5,"dishId":5}') . ')"',
            'title="Title changed by setAttributes when id 5"',
        ]);
})->with('buttonAttributes')->group('actionRules');

dataset('buttonAttributes', [
    'tailwind'       => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

dataset('buttonAttributesTailwind', [
    'tailwind'      => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'tailwind join' => [DishTableBase::class, (object) ['theme' => 'tailwind', 'join' => true]],
]);
