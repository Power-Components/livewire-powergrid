<?php

use function Pest\Livewire\livewire;
use PowerComponents\LivewirePowerGrid\Tests\{RulesEmitTable, RulesEmitToTable};

it('add rule \'emitTo\' when dishId == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        //page 1
        ->set('setUp.footer.perPage', 5)
        ->assertSee('$emitTo("dishes-table", "deletedEvent", {"dishId":5})')
        ->assertDontSee('$emitTo("dishes-table", "deletedEvent", {"dishId":4})')
        ->assertPayloadNotSet('eventId', ['dishId' => 5])
        ->call('deletedEvent', ['dishId' => 5])
        ->assertPayloadSet('eventId', ['dishId' => 5])

        //page 2
        ->call('setPage', 2)
        ->assertSee('$emitTo("dishes-table", "deletedEvent", {"dishId":6})')
        ->assertDontSee('$emitTo("dishes-table", "deletedEvent", {"dishId":1})')
        ->assertPayloadNotSet('deletedEvent', ['dishId' => 6])
        ->call('deletedEvent', ['dishId' => 6])
        ->assertPayloadSet('eventId', ['dishId' => 6]);
})->with('emitTo')->group('actionRules');

dataset('emitTo', [
    'tailwind'       => [RulesEmitToTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [RulesEmitToTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [RulesEmitToTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [RulesEmitToTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
