<?php

use function Pest\Livewire\livewire;
use PowerComponents\LivewirePowerGrid\Tests\RulesAttributesTable;

it('add many \'setAttributes\' when dish-id == 1', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtml([
            'class="text-center"',
        ])
        ->assertDontSee('bg-slate-200');
})->with('attributes')->group('actionRules');

it('add many \'setAttributes\' when dish-id == 2', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Peixada da chef Nábia')
        ->assertSeeHtml([
            'class="text-center bg-slate-200"',
            'wire:click="test({&quot;param1&quot;:2,&quot;dishId&quot;:2})"',
            'title="Title changed by setAttributes when id 2"',
        ]);
})->with('attributes')->group('actionRules');

it('add many \'setAttributes\' when dish-id == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Francesinha vegana')
        ->assertSeeHtml([
            'class="text-center bg-slate-500"',
            'wire:click="test({&quot;param1&quot;:5,&quot;dishId&quot;:5})"',
            'title="Title changed by setAttributes when id 5"',
        ]);
})->with('attributes')->group('actionRules');

it('check if there is no rule when dish-id == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Barco-Sushi da Sueli')
        ->assertDontSeeHtml([
            'class="text-center bg-slate-500"',
            'wire:click="test(',
            'title="Title changed by setAttributes',
        ]);
})->with('attributes')->group('actionRules');

dataset('attributes', [
    'tailwind'       => [RulesAttributesTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [RulesAttributesTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [RulesAttributesTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [RulesAttributesTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
