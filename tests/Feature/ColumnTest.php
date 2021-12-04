<?php

use function Pest\Livewire\livewire;

it('sorts by "name" and then by "id"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('Pastel de Nata')
        ->call('sortBy', $params->field)
        ->assertSeeHtml('Almôndegas ao Sugo')
        ->assertDontSeeHtml('Pastel de Nata')
        ->call('sortBy', 'id')
        ->assertSeeHtml('Pastel de Nata');
})->with('themes with name field');

it('searches data', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('Pastel de Nata')
        ->set('search', 'Sugo')
        ->assertSeeHtml('Almôndegas ao Sugo')
        ->assertDontSeeHtml('Pastel de Nata');
})->with('themes');

it('searches data as case insensitive', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('Pastel de Nata')
        ->set('search', 'sugo')
        ->assertSeeHtml('Almôndegas ao Sugo')
        ->assertDontSeeHtml('Pastel de Nata');
})->with('themes');
