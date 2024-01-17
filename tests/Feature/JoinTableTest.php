<?php

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{Concerns\Components\DishesTableWithJoin,
    Concerns\Components\DishesTableWithJoinNames};

it('properly sorts ASC/DESC with: string join column', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('setUp.footer.perPage', '10')
        ->call('sortBy', 'dishes.id')
        ->set('sortDirection', 'desc')
        ->assertSee('Sopas')
        ->call('sortBy', 'categories.name')
        ->set('sortDirection', 'asc')
        ->assertSee('Acompanhamentos');
})->with([
    'tailwind'  => [DishesTableWithJoin::class, 'tailwind'],
    'bootstrap' => [DishesTableWithJoin::class, 'bootstrap'],
]);

it('properly search join column with invalid table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('search', 'Pastel de Nata')
        ->assertSee('Pastel')
        ->assertDontSee('Sopas')
        # search in newCategories.name
        ->set('search', 'Peixe')
        ->assertSee('Peixe')
        ->assertDontSee([
            'Acompanhamentos',
            'Sobremesas',
        ]);
})->with([
    'tailwind'  => [DishesTableWithJoinNames::class, 'tailwind'],
    'bootstrap' => [DishesTableWithJoinNames::class, 'bootstrap'],
]);
