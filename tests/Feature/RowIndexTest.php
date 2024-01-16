<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesRowIndex;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('sorts by "name" and then by "id"', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->assertSeeHtmlInOrder([
            '<div>1</div>',
            '<div>Pastel de Nata</div>',
            '<div>2</div>',
            '<div>Peixada da chef Nábia</div>',
            '<div>3</div>',
            '<div>Carne Louca</div>',
            '<div>4</div>',
            '<div>Bife à Rolê</div>',
            '<div>5</div>',
            '<div>Francesinha vegana</div>',
        ])
        ->set('filters', filterInputText('Pastel', 'contains_not'))
        ->assertSeeHtmlInOrder([
            '<div>1</div>',
            '<div>Peixada da chef Nábia</div>',
            '<div>2</div>',
            '<div>Carne Louca</div>',
            '<div>3</div>',
            '<div>Bife à Rolê</div>',
            '<div>4</div>',
            '<div>Francesinha vegana</div>',
        ])
        ->set('filters', filterInputText('Carne', 'contains_not'))
        ->assertSeeHtmlInOrder([
            '<div>1</div>',
            '<div>Pastel de Nata</div>',
            '<div>2</div>',
            '<div>Peixada da chef Nábia</div>',
            '<div>3</div>',
            '<div>Bife à Rolê</div>',
            '<div>4</div>',
            '<div>Francesinha vegana</div>',
        ]);
})->with('row_index');

dataset('row_index', [
    'tailwind'  => [DishesRowIndex::class, 'tailwind'],
    'bootstrap' => [DishesRowIndex::class, 'bootstrap'],
]);
