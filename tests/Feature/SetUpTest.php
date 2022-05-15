<?php

use function Pest\Livewire\livewire;
use PowerComponents\LivewirePowerGrid\Tests\DishesSetUpTable;

it('show includeViewOnTop Header', function () {
    livewire(DishesSetUpTable::class, ['testHeader' => true])
        ->assertSeeHtmlInOrder([
            '<div>Included By Header Top</div>',
            'Pastel de Nata',
            '<div>Included By Header Bottom</div>',
        ])
        ->assertDontSeeHtml('<div>Included By Footer Top</div>')
        ->assertDontSeeHtml('<div>Included By Footer Bottom</div>');
});

it('show includeViewOnTop Footer', function () {
    livewire(DishesSetUpTable::class, ['testFooter' => true])
        ->assertSeeHtmlInOrder([
            'Pastel de Nata',
            '<div>Included By Footer Top</div>',
            '<div>Included By Footer Bottom</div>',
        ])
        ->assertDontSeeHtml('<div>Included By Header Top</div>')
        ->assertDontSeeHtml('<div>Included By Header Bottom</div>');
});
