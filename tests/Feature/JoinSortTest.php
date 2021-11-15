<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTableWithJoin;

use function Pest\Livewire\livewire;

it('properly sorts ASC/DESC with: string join column', function () {
    livewire(DishesTableWithJoin::class)
        ->set('perPage', '10')
        ->call('sortBy', 'dishes.id')
        ->set('sortDirection', 'desc')
        ->assertSeeText('Sopas')
        ->assertSeeText('Sobremesas')
        ->call('sortBy', 'categories.name')
        ->set('sortDirection', 'asc')
        ->assertSeeText('Acompanhamentos');
})->skip();

