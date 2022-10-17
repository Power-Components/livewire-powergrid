<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\DishesActionTable;

it('properly displays "openModal" on edit button', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSee('$emit("openModal", "edit-stock", {"dishId":1})')
        ->assertSee('$emit("openModal", "edit-stock", {"dishId":2})')
        ->assertDontSee('$emit("openModal", "edit-stock", {"dishId":7})')
        ->call('setPage', 2)
        ->assertDontSee('$emit("openModal", "edit-stock", {"dishId":6})')
        ->assertDontSee('$emit("openModal", "edit-stock", {"dishId":1})');
})->with('action')->group('action');

it('properly displays "$dispatch" on edit button', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSee('$dispatch("browserEvent", {"dishId":1})')
        ->assertSee('$dispatch("browserEvent", {"dishId":2})')
        ->assertDontSee('$dispatch("browserEvent", {"dishId":7})')
        ->call('setPage', 2)
        ->assertDontSee('$dispatch("browserEvent", {"dishId":6})')
        ->assertDontSee('$dispatch("browserEvent", {"dishId":1})');
})->with('action')->group('action');

it('properly displays "deletedEvent" on delete button', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        //page 1
        ->set('setUp.footer.perPage', 5)
        ->assertSee('$emit("deletedEvent", {"dishId":1})')
        ->assertDontSee('$emit("deletedEvent", {"dishId":6})')
        ->assertPayloadNotSet('eventId', ['dishId' => 1])
        ->call('deletedEvent', ['dishId' => 1])
        ->assertPayloadSet('eventId', ['dishId' => 1])

        //page 2
        ->call('setPage', 2)
        ->assertSee('$emit("deletedEvent", {"dishId":6})')
        ->assertDontSee('$emit("deletedEvent", {"dishId":1})')
        ->assertPayloadNotSet('deletedEvent', ['dishId' => 6])
        ->call('deletedEvent', ['dishId' => 6])
        ->assertPayloadSet('eventId', ['dishId' => 6]);
})->with('action')->group('action');

it('properly displays "deletedEvent" on delete button from emitTo', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        //page 1
        ->set('setUp.footer.perPage', 5)
        ->assertSee('$emitTo("dishes-table", "deletedEvent", {"dishId":1})')
        ->assertDontSee('$emitTo("dishes-table", "deletedEvent", {"dishId":6})')
        ->assertPayloadNotSet('eventId', ['dishId' => 1])
        ->call('deletedEvent', ['dishId' => 1])
        ->assertPayloadSet('eventId', ['dishId' => 1])

        //page 2
        ->call('setPage', 2)
        ->assertSee('$emitTo("dishes-table", "deletedEvent", {"dishId":6})')
        ->assertDontSee('$emitTo("dishes-table", "deletedEvent", {"dishId":1})')
        ->assertPayloadNotSet('deletedEvent', ['dishId' => 6])
        ->call('deletedEvent', ['dishId' => 6])
        ->assertPayloadSet('eventId', ['dishId' => 6]);
})->with('action')->group('action');

it('properly displays "bladeComponent" on bladeComponent button', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml('<svg dish-id="1"')
        ->assertSeeHtml('<svg dish-id="2"')
        ->assertSeeHtml('<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />')
        ->assertDontSeeHtml('<svg dish-id="7"')
        ->call('setPage', 2)
        ->assertDontSeeHtml('<svg dish-id="6"')
        ->assertDontSeeHtml('<svg dish-id="1"');
})->with('action')->group('action');

it('properly displays "id" on button', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtml('id="open-modal-1"')
        ->assertDontSeeHtml('id="open-modal-2"')
        ->set('search', 'Peixada da chef Nábia')
        ->assertSeeHtml('id="open-modal-2"')
        ->assertDontSeeHtml('id="open-modal-1"');
})->with('action')->group('action');

dataset('action', [
    'tailwind'       => [DishesActionTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [DishesActionTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [DishesActionTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [DishesActionTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
