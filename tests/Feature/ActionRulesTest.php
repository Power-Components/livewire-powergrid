<?php

use function Pest\Livewire\livewire;
use PowerComponents\LivewirePowerGrid\Tests\DishesDetailRowTable;

it('add rule \'redirect\' when out of stock and dishId !== 8', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', 10)
        ->assertSeeHtmlInOrder(['<a ', 'href="https://www.dish.test/sorry-out-of-stock?dish=6'])
        ->assertSeeHtmlInOrder(['<a ', 'href="https://www.dish.test/sorry-out-of-stock?dish=7'])
        ->assertSeeHtml('$emit("openModal", "edit-stock-for-rules", {"dishId":8})')
        ->assertDontSee('www.dish.test/sorry-out-of-stock?dish=8')
        ->assertSeeHtmlInOrder(['<a ', 'disabled'])
        ->assertSeeHtmlInOrder(['<a ', 'href="https://www.dish.test/sorry-out-of-stock?dish=10']);
})->with('rules');

it('add rule \'hide\' when dishId == 2', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->assertSeeHtmlInOrder([
            'style="display: block"',
            '$emit("openModal", "edit-stock-for-rules", {"dishId":1})',
        ])
        ->assertSeeHtmlInOrder([
            'style="display: none"',
            '$emit("openModal", "edit-stock-for-rules", {"dishId":2})',
        ]);
})->with('rules');

it('add rule \'setAttribute\' bg-red when out of stock', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->assertSee('bg-red-100 text-red-800');
})->with('rules');

it('add rule \'caption\' when dish out of stock', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtml('<div id="edit">Edit for Rules</div>')
        ->assertDontSeeHtml('cation edit for id 4')
        ->set('search', 'Bife à Rolê')
        ->assertDontSeeHtml('<div id="edit">Edit for Rules</div>')
        ->assertSeeHtml('cation edit for id 4');
})->with('rules');

it('add rule \'emit\' when dishId == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 10)
        ->set('search', 'Francesinha vegana')
        ->assertSeeHtml('$emit("toggleEvent", {"dishId":5})');
})->with('rules')->skip('This test needs to be refactored');

it('add rule \'disable\' when dishId == 9', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Polpetone Filé Mignon')
        ->assertSeeHtmlInOrder([
            '<a',
            'disabled',
            'class="text-center bg-custom-300"',
        ]);
})->with('rules');

it('add rule \'toggleDetail\' when dishId == 3', function () {
    livewire(DishesDetailRowTable::class)
        ->assertSee('Pastel de Nata')
        ->assertDontSeeHtml([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ]) // show detail row #1
        ->call('toggleDetail', 1)
        ->assertSeeHtmlInOrder([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => true,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ])
        ->call('toggleDetail', 3)
        ->assertSeeHtmlInOrder([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
            '<div>Id 3</div>',
            '<div>Options {"name":"Luan","fromActionRule":true}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => true,
            2 => false,
            3 => true,
            4 => false,
            5 => false,
        ]);
});

it('add rule \'bladeComponent\' when dish-id == 10', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 10)
        ->assertSeeHtml('<svg dish-id="10"')
        ->assertSeeHtml('<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />')
        ->assertDontSeeHtml('<svg dish-id="1"');
})->with('rules');

it('add many \'setAttributes\' when dish-id == 11', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Bife à Parmegiana')
        ->assertSee('class="text-center bg-custom-300"', false)
        ->assertSee('title="Title changed by setAttributes"', false)
        ->assertSee('wire:click="test({&quot;param1&quot;:1,&quot;dishId&quot;:11})"', false);
})->with('rules');
