<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('properly displays "full" showRecordCount')
    ->livewire(DishesTable::class)
    ->assertSeeTextInOrder(['Showing', '1', 'to', '10', 'of', '15', 'Results']);

it('properly displays "short" showRecordCount')
    ->livewire(DishesTable::class)
    ->set('setUp.footer.recordCount', 'short')
    ->assertSeeTextInOrder(['1', '-', '10', '|', '15']);

it('properly displays "min" showRecordCount')
    ->livewire(DishesTable::class)
    ->set('setUp.footer.recordCount', 'min')
    ->assertSeeTextInOrder(['1', '10']);

it('properly changes records and displays per page')
    ->livewire(DishesTable::class)
    ->set('setUp.footer.perPage', '11')
    ->assertSeeTextInOrder(['Showing', '1', 'to', '11', 'of', '15', 'Results'])
    ->set('setUp.footer.perPage', '12')
    ->assertSeeTextInOrder(['Showing', '1', 'to', '12', 'of', '15', 'Results'])
    ->set('setUp.footer.perPage', '0') //All items
    ->assertSeeTextInOrder(['Showing', '1', 'to', '15', 'of', '15', 'Results'])
    ->assertSeeHtml('Pastel de Nata');

it('navigates when click on "page #2"')
    ->livewire(DishesTable::class)
    ->assertSeeHtml('Pastel de Nata')
    ->call('gotoPage', '2')
    ->assertSeeHtml('Bife à Parmegiana')
    ->assertDontSeeHtml('Pastel de Nata');

it('navigates when click on "next page"')
    ->livewire(DishesTable::class)
    ->assertSeeHtml('Pastel de Nata')
    ->call('nextPage')
    ->assertSeeHtml('Bife à Parmegiana')
    ->assertDontSeeHtml('Pastel de Nata');

it('navigates when click on "goToPage" and "previousPage"')
    ->livewire(DishesTable::class)
    ->assertSeeHtml('Pastel de Nata')
    ->call('gotoPage', 2)
    ->assertSeeHtml('Bife à Parmegiana')
    ->call('previousPage')
    ->assertSeeHtml('Pastel de Nata')
    ->assertDontSeeHtml('Bife à Parmegiana');

it('displays next links ">" and ">>"')
    ->livewire(DishesTable::class)
    ->set('setUp.footer.perPage', '4')

    ->assertSeeHtml('wire:click="nextPage"')
    //page #2
    ->call('gotoPage', '2')
    ->assertSeeHtml('wire:click="nextPage"');

it('displays previous links "<" and "<<"')
    ->livewire(DishesTable::class)
    ->assertDontSeeHtml('wire:click="previousPage"')
    //page #2
    ->call('gotoPage', '2')
    ->assertSeeHtml('wire:click="previousPage"');

it('searches for something that is not on the current page')
    ->livewire(DishesTable::class)
    ->assertSeeHtml('Francesinha vegana')
    ->call('gotoPage', 2)
    ->assertSeeHtml('Bife à Parmegiana')
    ->assertDontSeeHtml('Francesinha vegana')
    ->set('search', 'Francesinha vegana')
    ->assertDontSeeHtml('Bife à Parmegiana')
    ->assertSeeHtml('Francesinha vegana');

it('properly paginates', function () {
    $component = powergrid();

    $component->setUp = [
        'footer' => ['perPage' => 5],
    ];
    $pagination = $component->fillData();

    expect($pagination->items())->toHaveCount(5)
        ->and($pagination->first()->name)->toBe('Pastel de Nata')
        ->and($pagination->total())->toBe(15)
        ->and($pagination->perPage())->toBe(5);
});
