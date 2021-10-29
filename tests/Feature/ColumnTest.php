<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it("searches data")
    ->livewire(DishesTable::class)
    ->assertSeeHtml('Pastel de Nata')
    ->set('search', 'sugo')
    ->assertSeeHtml('Almôndegas ao Sugo')
    ->assertDontSeeHtml('Pastel de Nata');
