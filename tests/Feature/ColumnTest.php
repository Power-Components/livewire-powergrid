<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('sorts by "name"')
    ->livewire(DishesTable::class)
    ->assertSeeHtml('Pastel de Nata')
    ->call('sortBy', 'name')
    ->assertSeeHtml('Almôndegas ao Sugo')
    ->assertDontSeeHtml('Pastel de Nata')
    ->call('sortBy', 'id')
    ->assertSeeHtml('Pastel de Nata');
