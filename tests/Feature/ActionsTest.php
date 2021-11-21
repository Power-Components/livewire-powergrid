<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('updates data')
    ->livewire(DishesTable::class)
    ->call('gotoPage', '11')
    ->assertDontSeeHtml('Calçots')
    ->call('gotoPage', '1')
    ->call('update', ['id' => '101', 'field' => 'name', 'value' => 'Calçots'])
    ->call('gotoPage', '11')
    ->assertSeeHtml('Calçots');

it('properly displays "openModal" on edit button')
    ->livewire(DishesTable::class)
    ->set('perPage', 5)
    ->assertSeeHtml('$emit("openModal", "edit-dish", {"dishId":1})')
    ->assertDontSeeHtml('$emit("openModal", "edit-dish", {"dishId":6})')
    ->call('setPage', 2)
    ->assertSeeHtml('$emit("openModal", "edit-dish", {"dishId":6})')
    ->assertDontSeeHtml('$emit("openModal", "edit-dish", {"dishId":1})');

it('properly displays "deletedEvent" on delete button')
    ->livewire(DishesTable::class)
    //page 1
    ->set('perPage', 5)
    ->assertSeeHtml('$emit("deletedEvent", {"dishId":1})')
    ->assertDontSeeHtml('$emit("deletedEvent", {"dishId":6})')
    ->assertPayloadNotSet('eventId', ['dishId' => 1])
    ->call('deletedEvent', ['dishId' => 1])
    ->assertPayloadSet('eventId', ['dishId' => 1])

    //page 2
    ->call('setPage', 2)
    ->assertSeeHtml('$emit("deletedEvent", {"dishId":6})')
    ->assertDontSeeHtml('$emit("deletedEvent", {"dishId":1})')
    ->assertPayloadNotSet('deletedEvent', ['dishId' => 6])
    ->call('deletedEvent', ['dishId' => 6])
    ->assertPayloadSet('eventId', ['dishId' => 6]);
