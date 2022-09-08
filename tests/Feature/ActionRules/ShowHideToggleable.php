<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\RulesShowHideToggleable;

it('hides all Toggleables except for id #2', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('testActionRules', [
            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id < 50)
                ->hideToggleable(),

            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id == 2)
                ->showToggleable(),
        ])
        ->assertSeeHtmlInOrder([
            "id: '1',",
            'isHidden: true,',
        ])
        ->assertSeeHtmlInOrder([
            "id: '2',",
            'isHidden: false,',
        ])
        ->assertSeeHtmlInOrder([
            "id: '3',",
            'isHidden: true,',
        ]);
})->with('ShowHideToggleable')->group('actionRules');

it('hides only toggleables on id #2', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('testActionRules', [
            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id < 50)
                ->showToggleable(),

            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id == 2)
                ->hideToggleable(),
        ])
        ->assertSeeHtmlInOrder([
            "id: '1',",
            'isHidden: false,',
        ])
        ->assertSeeHtmlInOrder([
            "id: '2',",
            'isHidden: true,',
        ])
        ->assertSeeHtmlInOrder([
            "id: '3',",
            'isHidden: false,',
        ]);
})->with('ShowHideToggleable')->group('actionRules');

dataset('ShowHideToggleable', [
    'tailwind'       => [RulesShowHideToggleable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [RulesShowHideToggleable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [RulesShowHideToggleable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [RulesShowHideToggleable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
