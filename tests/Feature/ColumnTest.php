<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('sorts by "name" and then by "id"')
    ->livewire(DishesTable::class)
    ->assertSeeHtml('Pastel de Nata')
    ->call('sortBy', 'name')
    ->assertSeeHtml('Almôndegas ao Sugo')
    ->assertDontSeeHtml('Pastel de Nata')
    ->call('sortBy', 'id')
    ->assertSeeHtml('Pastel de Nata');

it("searches data")
    ->livewire(DishesTable::class)
    ->assertSeeHtml('Pastel de Nata')
    ->set('search', 'sugo')
    ->assertSeeHtml('Almôndegas ao Sugo')
    ->assertDontSeeHtml('Pastel de Nata');
