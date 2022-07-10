<?php

use function Pest\Livewire\livewire;

$expectedMultiSelect = <<<HTML
    x-data="pgMultiSelect({
        data: JSON.parse(atob('W3sidmFsdWUiOnsiaWQiOjEsIm5hbWUiOiJDYXJuZXMifX0seyJ2YWx1ZSI6eyJpZCI6MiwibmFtZSI6IlBlaXhlIn19LHsidmFsdWUiOnsiaWQiOjMsIm5hbWUiOiJUb3J0YXMifX0seyJ2YWx1ZSI6eyJpZCI6NCwibmFtZSI6IkFjb21wYW5oYW1lbnRvcyJ9fSx7InZhbHVlIjp7ImlkIjo1LCJuYW1lIjoiTWFzc2FzIn19XQ==')),
        value: 'id',
        text: 'name',
        tableName: 'default',
        dataField: 'category_id',
        selected: JSON.parse(atob('W10='))
HTML;

it('property displays the results and options', function (string $component) use ($expectedMultiSelect) {
    livewire($component)->assertSeeHtml($expectedMultiSelect);
})->with('themes');

it('properly filter with category_id - Carnes selected', function (string $component) use ($expectedMultiSelect) {
    livewire($component)
        ->assertSeeHtml($expectedMultiSelect)
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

it('properly filter with category_id - Carnes and Peixe selected', function (string $component) use ($expectedMultiSelect) {
    livewire($component)
        ->set('setUp.footer.perPage', '100')
        ->assertSeeHtml($expectedMultiSelect)
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
