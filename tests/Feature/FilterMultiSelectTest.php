<?php

use function Pest\Livewire\livewire;

it('property displays the results and options', function (string $component) {
    livewire($component)
        ->assertSeeHtml([
            'x-data="pgMultiSelect',
            'data: [{&quot;value&quot;:{&quot;id&quot;:1,&quot;name&quot;:&quot;Carnes&quot;}},{&quot;value&quot;:{&quot;id&quot;:2,&quot;name&quot;:&quot;Peixe&quot;}},{&quot;value&quot;:{&quot;id&quot;:3,&quot;name&quot;:&quot;Tortas&quot;}},{&quot;value&quot;:{&quot;id&quot;:4,&quot;name&quot;:&quot;Acompanhamentos&quot;}},{&quot;value&quot;:{&quot;id&quot;:5,&quot;name&quot;:&quot;Massas&quot;}}],',
            'value: \'id\'',
            'text: \'name\'',
            'tableName: \'default\'',
            'dataField: \'category_id\'',
            'selected: \'[]\'',
        ]);
})->with('themes');

it('properly filter with category_id - Carnes selected', function (string $component) {
    livewire($component)
        ->assertSeeHtml([
            'x-data="pgMultiSelect',
            'data: [{&quot;value&quot;:{&quot;id&quot;:1,&quot;name&quot;:&quot;Carnes&quot;}},{&quot;value&quot;:{&quot;id&quot;:2,&quot;name&quot;:&quot;Peixe&quot;}},{&quot;value&quot;:{&quot;id&quot;:3,&quot;name&quot;:&quot;Tortas&quot;}},{&quot;value&quot;:{&quot;id&quot;:4,&quot;name&quot;:&quot;Acompanhamentos&quot;}},{&quot;value&quot;:{&quot;id&quot;:5,&quot;name&quot;:&quot;Massas&quot;}}],',
            'value: \'id\'',
            'text: \'name\'',
            'tableName: \'default\'',
            'dataField: \'category_id\'',
            'selected: \'[]\'',
        ])
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
        ->assertSeeHtml([
            'x-data="pgMultiSelect',
            'data: [{&quot;value&quot;:{&quot;id&quot;:1,&quot;name&quot;:&quot;Carnes&quot;}},{&quot;value&quot;:{&quot;id&quot;:2,&quot;name&quot;:&quot;Peixe&quot;}},{&quot;value&quot;:{&quot;id&quot;:3,&quot;name&quot;:&quot;Tortas&quot;}},{&quot;value&quot;:{&quot;id&quot;:4,&quot;name&quot;:&quot;Acompanhamentos&quot;}},{&quot;value&quot;:{&quot;id&quot;:5,&quot;name&quot;:&quot;Massas&quot;}}],',
            'value: \'id\'',
            'text: \'name\'',
            'tableName: \'default\'',
            'dataField: \'category_id\'',
            'selected: \'[]\'',
        ])
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
