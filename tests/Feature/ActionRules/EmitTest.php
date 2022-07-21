<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\RulesEmitTable;

it('add rule \'emit\' when dishId == 5', function (string $component, object $params) {
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
                ->emit('toggleEvent', ['dishId' => 'id']),
        ])
        ->set('setUp.footer.perPage', 10)
        ->set('search', 'Francesinha vegana')
        ->assertSee('$emit("toggleEvent", {"dishId":5})')
        ->assertPayloadNotSet('eventId', ['dishId' => 5])
        ->call('deletedEvent', ['dishId' => 5])
        ->assertPayloadSet('eventId', ['dishId' => 5]);
})->with('emit')->group('actionRules');

dataset('emit', [
    'tailwind'       => [RulesEmitTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [RulesEmitTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [RulesEmitTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [RulesEmitTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
