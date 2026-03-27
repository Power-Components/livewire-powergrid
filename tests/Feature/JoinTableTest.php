<?php

use PowerComponents\LivewirePowerGrid\Tests\{Concerns\Components\DishesTableWithJoin,
    Concerns\Components\DishesTableWithJoinNames};
use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, DaisyUI, Tailwind};

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('properly sorts ASC/DESC with: string join column', function (string $component, string $theme) {
    livewire($component)
        ->call('setTestThemeClass', $theme)
        ->set('setUp.footer.perPage', '10')
        ->call('sortBy', 'dishes.id')
        ->set('sortDirection', 'desc')
        ->assertSee('Sopas')
        ->call('sortBy', 'categories.name')
        ->set('sortDirection', 'asc')
        ->assertSee('Acompanhamentos');
})->with([
    'tailwind' => [DishesTableWithJoin::class, Tailwind::class],
    'bootstrap' => [DishesTableWithJoin::class, Bootstrap5::class],
    'daisyui' => [DishesTableWithJoin::class, DaisyUI::class],
]);

it('properly search join column with invalid table', function (string $component, string $theme) {
    livewire($component)
        ->call('setTestThemeClass', $theme)
        ->set('search', 'Pastel de Nata')
        ->assertSee('Pastel')
        ->assertDontSee('Sopas')
        // search in newCategories.name
        ->set('search', 'Peixe')
        ->assertSee('Peixe')
        ->assertDontSee([
            'Acompanhamentos',
            'Sobremesas',
        ]);
})->with([
    'tailwind' => [DishesTableWithJoinNames::class, Tailwind::class],
    'bootstrap' => [DishesTableWithJoinNames::class, Bootstrap5::class],
    'daisyui' => [DishesTableWithJoinNames::class, DaisyUI::class],
]);
