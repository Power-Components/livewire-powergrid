<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTableWithJoin;

it('properly sorts ASC/DESC with: string join column')
    ->livewire(DishesTableWithJoin::class)
    ->set('perPage', '10')
    ->call('sortBy', 'dishes.id')
    ->set('sortDirection', 'desc')
    ->assertSee('102')
    ->assertSee('101')
    ->assertDontSee('91')
    ->call('sortBy', 'categories.name')
    ->set('sortDirection', 'asc')
    ->assertSee('19')
    ->assertSee('Tortas')
    ->assertSee('Sopas');
