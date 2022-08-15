<?php

use function Pest\Livewire\livewire;

it('properly filter with category_id - Carnes selected', function (string $component) {
    livewire($component)
        ->set('filters', [
            'multi_select' => [
                'category_id' => [
                    1,
                ],
            ],
        ])
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertSeeInOrder([
            'Peixada da chef Nábia',
            'Carne Louca',
            'Bife à Rolê',
        ]);
})->with('themes');

it('properly filter with category_id - Carnes and Peixe selected', function (string $component) {
    livewire($component)
        ->set('setUp.footer.perPage', '100')
        ->set('filters', [
            'multi_select' => [
                'category_id' => [
                    1,
                ],
            ],
        ])
        ->assertDontSee('Torta Campestre de Frango')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertSeeInOrder([
            'Peixada da chef Nábia',
            'Carne Louca',
            'Bife à Rolê',
        ])
        ->set('filters', [
            'multi_select' => [
                'category_id' => [
                    1,
                    3,
                ],
            ],
        ])
        ->assertSee('Torta Campestre de Frango');
})->with('themes');
