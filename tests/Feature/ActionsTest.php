<?php

use Illuminate\Support\Js;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;

$component = new class () extends DishTableBase {
    public function executeCall(array $params)
    {
    }

    public function actions($row): array
    {
        return [
            Button::make('edit')
                ->slot('call: ' . $row->id)
                ->call('executeCall', [
                    'params' => ['id' => $row->id],
                ]),
        ];
    }
};

dataset('action', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

todo('properly displays "call" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml("\$call('executeCall', " . Js::from([
            'params' => ['id' => 1],
        ]) . ")")
        ->assertSeeHtml("\$call('executeCall', " . Js::from([
            'params' => ['id' => 2],
        ]) . ")")
        ->assertDontSeeHtml("\$call('executeCall', " . Js::from([
            'params' => ['id' => 7],
        ]) . ")")
        ->call('setPage', 2)
        ->assertSeeHtml("\$call('executeCall', " . Js::from([
            'params' => ['id' => 7],
        ]) . ")")
        ->assertDontSeeHtml("\$call('executeCall', " . Js::from([
            'params' => ['id' => 1],
        ]) . ")");
})->with('action')->group('action');

todo('properly displays "$dispatch" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join'        => $params->join,
        'actionsTest' => [
            Button::add('dispatch')
                ->id('dispatch')
                ->slot('Dispatch')
                ->class('text-center')
                ->dispatch('browserEvent', ['dishId' => 'id']),
        ],
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSee('$dispatch("browserEvent", {"dishId":1})')
        ->assertSee('$dispatch("browserEvent", {"dishId":2})')
        ->assertDontSee('$dispatch("browserEvent", {"dishId":7})')
        ->call('setPage', 2)
        ->assertDontSee('$dispatch("browserEvent", {"dishId":6})')
        ->assertDontSee('$dispatch("browserEvent", {"dishId":1})');
})->with('action')->group('action');

todo('properly displays "$emit" on delete button', function (string $component, object $params) {
    livewire($component, [
        'join'        => $params->join,
        'actionsTest' => [
            Button::add('emit')
                ->id('emit')
                ->slot('Delete')
                ->class('text-center')
                ->dispatch('deletedEvent', ['dishId' => 'id']),
        ],
    ])
        ->call($params->theme)
        //page 1
        ->set('setUp.footer.perPage', 5)
        ->assertSee('$emit("deletedEvent", {"dishId":1})')
        ->assertDontSee('$emit("deletedEvent", {"dishId":6})')
       // ->assertPayloadNotSet('eventId', ['dishId' => 1])
        ->call('deletedEvent', ['dishId' => 1])
       // ->assertPayloadSet('eventId', ['dishId' => 1])

        //page 2
        ->call('setPage', 2)
        ->assertSee('$emit("deletedEvent", {"dishId":6})')
        ->assertDontSee('$emit("deletedEvent", {"dishId":1})')
       // ->assertPayloadNotSet('deletedEvent', ['dishId' => 6])
        ->call('deletedEvent', ['dishId' => 6]);
    //  ->assertPayloadSet('eventId', ['dishId' => 6]);
})->with('action')->group('action');

todo('properly displays "$emitTo" on delete button from emitTo', function (string $component, object $params) {
    livewire($component, [
        'join'        => $params->join,
        'actionsTest' => [
            Button::add('emitTo')
                ->id('emit-to')
                ->slot('EmitTo')
                ->class('text-center')
                ->dispatchTo('dishes-table', 'deletedEvent', ['dishId' => 'id']),
        ],
    ])
        ->call($params->theme)
        //page 1
        ->set('setUp.footer.perPage', 5)
        ->assertSee('$emitTo("dishes-table", "deletedEvent", {"dishId":1})')
        ->assertDontSee('$emitTo("dishes-table", "deletedEvent", {"dishId":6})')
      //  ->assertPayloadNotSet('eventId', ['dishId' => 1])
        ->call('deletedEvent', ['dishId' => 1])
      //  ->assertPayloadSet('eventId', ['dishId' => 1])

        //page 2
        ->call('setPage', 2)
        ->assertSee('$emitTo("dishes-table", "deletedEvent", {"dishId":6})')
        ->assertDontSee('$emitTo("dishes-table", "deletedEvent", {"dishId":1})')
      //  ->assertPayloadNotSet('deletedEvent', ['dishId' => 6])
        ->call('deletedEvent', ['dishId' => 6]);
    //  ->assertPayloadSet('eventId', ['dishId' => 6]);
})->with('action')->group('action');

it('properly displays "bladeComponent" on bladeComponent button', function (string $component, object $params) {
    livewire($component, [
        'join'        => $params->join,
        'actionsTest' => [
            Button::add('bladeComponent')
                ->bladeComponent('livewire-powergrid::icons.arrow', ['dish-id' => 'id']),
        ],
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml('<svg', 'dish-id="1"')
        ->assertSeeHtml('<svg', 'dish-id="2"')
        ->assertSeeHtml('<path', 'stroke-linecap="round"', 'stroke-linejoin="round"', 'd="M9 5l7 7-7 7"', '/>')
        ->assertDontSeeHtml('<svg dish-id="7"')
        ->call('setPage', 2)
        ->assertDontSeeHtml('<svg dish-id="6"')
        ->assertDontSeeHtml('<svg dish-id="1"');
})->with('action')->group('action');

it('properly displays "id" on button', function (string $component, object $params) {
    livewire($component, [
        'join'        => $params->join,
        'actionsTest' => [
            Button::add('openModal')
                ->id('open-modal')
                ->slot('openModal')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => 'id']),
        ],
    ])
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtml('id="open-modal-1"')
        ->assertDontSeeHtml('id="open-modal-2"')
        ->set('search', 'Peixada da chef Nábia')
        ->assertSeeHtml('id="open-modal-2"')
        ->assertDontSeeHtml('id="open-modal-1"');
})->with('action')->group('action');
