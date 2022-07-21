<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\DishesSetUpTable;

it('show includeViewOnTop/Bottom - Header', function () {
    livewire(DishesSetUpTable::class, ['testHeader' => true])
        ->assertSeeHtmlInOrder([
            '<div>Included By Header Top</div>',
            'Pastel de Nata',
        ])
        ->assertSeeHtml('<div>Included By Header Bottom</div>')
        ->assertDontSeeHtml('<div>Included By Footer Top</div>')
        ->assertDontSeeHtml('<div>Included By Footer Bottom</div>');
});

it('show includeViewOnTop/Bottom - Footer', function () {
    livewire(DishesSetUpTable::class, ['testFooter' => true])
        ->assertSeeHtmlInOrder([
            'Pastel de Nata',
            '<div>Included By Footer Top</div>',
            '<div>Included By Footer Bottom</div>',
        ])
        ->assertDontSeeHtml('<div>Included By Header Top</div>')
        ->assertDontSeeHtml('<div>Included By Header Bottom</div>');
});
