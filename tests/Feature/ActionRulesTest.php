<?php

use function Pest\Livewire\livewire;

it('add rule \'disable\' when dishId == 9', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Polpetone Filé Mignon')
        ->assertSeeHtmlInOrder([
            '<a',
            'disabled',
            'class="text-center bg-custom-300"',
        ]);
})->with('rules')->skip('refactoring');
