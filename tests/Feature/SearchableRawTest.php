<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesSearchableRawTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('searches data using whereRaw on sqlite', function (string $component, object $params) {
    livewire($component, ['database' => 'sqlite'])
        ->call('setTestThemeClass', $params->theme)
        ->set('search', '09/09/2021')
        ->assertSee('Polpetone Filé Mignon')
        ->assertDontSee('Barco-Sushi Simples')
        ->assertDontSee('No records found')
        # Id: 2
        ->set('search', '#2')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('No records found')
        # 09/09/2046
        ->set('search', '09/09/2046') //No dishes in this date
        ->assertSee('No records found')
        # 06/2026
        ->set('search', '06/2026')
        ->assertSee('Francesinha')
        ->assertDontSee('Polpetone Filé Mignon')
        # $60.50
        ->set('search', 'R$ 60,50')
        ->assertSee('Francesinha')
        ->assertDontSee('Polpetone Filé Mignon')
        # 1,500.40
        ->set('search', '1.500,40')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Francesinha');
})->with('searchable_raw_themes')->requiresSQLite();

it('searches data using whereRaw on mysql', function (string $component, object $params) {
    livewire($component, ['database' => 'mysql'])
        ->call('setTestThemeClass', $params->theme)
        ->call('setTestThemeClass', $params->theme)
        ->set('search', '09/09/2021')
        ->assertSee('Polpetone Filé Mignon')
        ->assertDontSee('Barco-Sushi Simples')
        ->assertDontSee('No records found')
        # Id: 2
        ->set('search', '#2')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('No records found')
        # 09/09/2046
        ->set('search', '09/09/2046') //No dishes in this date
        ->assertSee('No records found')
        # 06/2026
        ->set('search', '06/2026')
        ->assertSee('Francesinha')
        ->assertDontSee('Polpetone Filé Mignon')
        # $60.50
        ->set('search', 'R$ 60,50')
        ->assertSee('Francesinha')
        ->assertDontSee('Polpetone Filé Mignon')
        # 1,500.40
        ->set('search', '1.500,40')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Francesinha');
})->with('searchable_raw_themes')->requiresMySQL();

it('searches data using whereRaw on pgsql', function (string $component, object $params) {
    livewire($component, ['database' => 'pgsql'])
        ->call('setTestThemeClass', $params->theme)
        ->set('search', '09/09/2021')
        ->assertSee('Polpetone Filé Mignon')
        ->assertDontSee('Barco-Sushi Simples')
        ->assertDontSee('No records found')
        # Id: 2
        ->set('search', '#2')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('No records found')
        # 09/09/2046
        ->set('search', '09/09/2046') //No dishes in this date
        ->assertSee('No records found')
        # 06/2026
        ->set('search', '06/2026')
        ->assertSee('Francesinha')
        ->assertDontSee('Polpetone Filé Mignon')
        # $60.50
        ->set('search', 'R$ 60,50')
        ->assertSee('Francesinha')
        ->assertDontSee('Polpetone Filé Mignon')
        # 1,500.40
        ->set('search', '1.500,40')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Francesinha');
})->with('searchable_raw_themes')->requiresPostgreSQL();

dataset('searchable_raw_themes', [
    'tailwind'  => [DishesSearchableRawTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
    'bootstrap' => [DishesSearchableRawTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
]);
