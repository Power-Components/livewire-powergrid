<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\DishesTableWithJoin;

it('properly sorts ASC/DESC with: string join column', function () {
    livewire(DishesTableWithJoin::class)
        ->set('setUp.footer.perPage', '10')
        ->call('sortBy', 'dishes.id')
        ->set('sortDirection', 'desc')
        ->assertSee('Sopas')
        ->assertSee('Sobremesas')
        ->call('sortBy', 'categories.name')
        ->set('sortDirection', 'asc')
        ->assertSee('Acompanhamentos');
});
