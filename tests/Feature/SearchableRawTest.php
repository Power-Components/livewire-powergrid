<?php

use function Pest\Livewire\livewire;

it('searches data using whereRaw on sqlite', function (string $component, object $params) {
    livewire($component, ['database' => 'sqlite'])
        ->call($params->theme)
        ->set('search', '09/09/2021')
        ->assertSee('Polpetone Filé Mignon')
        ->assertDontSee('Barco-Sushi Simples')
        ->assertDontSee('No records found')
        # 09/09/2046
        ->set('search', '09/09/2046') //No dishes in this date
        ->assertSee('No records found')
        # 06/2026
        ->set('search', '06/2026')
        ->assertSee('Francesinha')
        ->assertDontSee('Polpetone Filé Mignon');
})->with('searchable-raw')->requiresSQLite();

it('searches data using whereRaw on mysql', function (string $component, object $params) {
    livewire($component, ['database' => 'mysql'])
        ->call($params->theme)
        ->call($params->theme)
        ->set('search', '09/09/2021')
        ->assertSee('Polpetone Filé Mignon')
        ->assertDontSee('Barco-Sushi Simples')
        ->assertDontSee('No records found')
        # 09/09/2046
        ->set('search', '09/09/2046') //No dishes in this date
        ->assertSee('No records found')
        # 06/2026
        ->set('search', '06/2026')
        ->assertSee('Francesinha')
        ->assertDontSee('Polpetone Filé Mignon');
})->with('searchable-raw')->requiresMySQL();

it('searches data using whereRaw on pgsql', function (string $component, object $params) {
    livewire($component, ['database' => 'pgsql'])
        ->call($params->theme)
        ->set('search', '09/09/2021')
        ->assertSee('Polpetone Filé Mignon')
        ->assertDontSee('Barco-Sushi Simples')
        ->assertDontSee('No records found')
        # 09/09/2046
        ->set('search', '09/09/2046') //No dishes in this date
        ->assertSee('No records found')
        # 06/2026
        ->set('search', '06/2026')
        ->assertSee('Francesinha')
        ->assertDontSee('Polpetone Filé Mignon');
})->with('searchable-raw')->requiresPostgreSQL();
