<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTableWithJoin;

it('properly sorts ASC/DESC with: string join column')
    ->livewire(DishesTableWithJoin::class)
    ->set('perPage', '10')
    ->call('sortBy', 'dishes.id')
    ->set('sortDirection', 'desc')
    ->assertSeeText('Sopas')
    ->assertSeeText('Sobremesas')
    ->call('sortBy', 'categories.name')
    ->set('sortDirection', 'asc')
    ->assertSeeText('Acompanhamentos');
