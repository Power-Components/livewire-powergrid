<?php

use function Pest\Livewire\livewire;

it('add rule \'redirect\' when out of stock and dishId !== 8', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('perPage', 10)
        ->assertSeeHtml('$emit("toggleEvent", {"dishId":5})')
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
        ->set('perPage', 10)
        ->set('search', 'Francesinha vegana')
        ->assertSeeHtml('$emit("toggleEvent", {"dishId":5})');
})->with('rules');

it('add rule \'disable\' when dishId == 9', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Polpetone Filé Mignon')
        ->assertSeeHtmlInOrder([
            '<a',
            'disabled',
            'class="text-center"',
        ]);
})->with('rules');
