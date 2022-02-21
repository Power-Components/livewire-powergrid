<?php

use function Pest\Livewire\livewire;

it('add rule \'redirect\' when out of stock and dishId !== 8', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('$emit("openModal", "edit-stock-for-rules", {"dishId":5})')
        ->assertDontSee('www.dish.test/sorry-out-of-stock?dish=5')
        ->assertSeeHtml('<a href="https://www.dish.test/sorry-out-of-stock?dish=6"')
        ->assertSeeHtml('<a href="https://www.dish.test/sorry-out-of-stock?dish=7"')
        ->assertSeeHtml('$emit("openModal", "edit-stock-for-rules", {"dishId":8})')
        ->assertDontSee('www.dish.test/sorry-out-of-stock?dish=8')
        ->assertSeeHtml('<a href="https://www.dish.test/sorry-out-of-stock?dish=9"')
        ->assertSeeHtml('<a href="https://www.dish.test/sorry-out-of-stock?dish=10"');
})->with('themes');

it('add rule \'hide\' when dishId == 2', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtmlInOrder([
            'style="display: block"',
            '$emit("openModal", "edit-stock-for-rules", {"dishId":1})',
        ])
        ->assertSeeHtmlInOrder([
            'style="display: none"',
            '$emit("openModal", "edit-stock-for-rules", {"dishId":2})',
        ]);
})->with('themes');

it('add rule \'setAttribute\' bg-red when out of stock', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSee('bg-red-100 text-red-800');
})->with('themes');

it('add rule \'caption\' when dish out of stock', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtml('<div id="edit">Edit for Rules</div>')
        ->assertDontSeeHtml('cation edit for id 4')
        ->set('search', 'Bife à Rolê')
        ->assertDontSeeHtml('<div id="edit">Edit for Rules</div>')
        ->assertSeeHtml('cation edit for id 4');
})->with('themes');

it('add rule \'emit\' when dishId == 5', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('search', 'Francesinha vegana')
        ->assertSeeHtml([
            '$emit("toggleEvent", {"dishId":5})',
        ]);
})->with('themes')->skip('conflict with action emit');

it('add rule \'disable\' when dishId == 9', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('search', 'Polpetone Filé Mignon')
        ->assertSeeHtmlInOrder([
            '<a href="https://www.dish.test/sorry-out-of-stock?dish=9"',
            'target="_blank"',
            'disabled',
            'class="text-center"',
        ])
        ->set('search', 'борщ')
        ->assertSeeHtmlInOrder([
            '<a href="https://www.dish.test/sorry-out-of-stock?dish=10"',
            'target="_blank"',
            'class="text-center"',
        ]);
})->with('themes');
