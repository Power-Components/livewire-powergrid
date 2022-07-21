<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\RulesToggleDetailTable;

it('collapse detail row', function () {
    livewire(RulesToggleDetailTable::class)
        ->assertSee('Pastel de Nata')
        ->assertDontSeeHtml([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ])
        // show detail row #1
        ->call('toggleDetail', 1)
        ->assertSeeHtmlInOrder([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => true,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ])
        // show detail row #2
        ->call('toggleDetail', 2)
        ->assertSet('setUp.detail.state', [
            1 => true,
            2 => true,
            3 => false,
            4 => false,
            5 => false,
        ])
        // see two details
        ->assertSeeHtmlInOrder([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
            '<div>Id 2</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        // collapse detail row #2
        ->call('toggleDetail', 2)
        ->assertSet('setUp.detail.state', [
            1 => true,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ])
        ->assertDontSeeHtml([
            '<div>Id 2</div>',
        ])
        // see detail row #1
        ->assertSeeHtml([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        // collapse detail row #1
        ->call('toggleDetail', 1)
        ->assertSet('setUp.detail.state', [
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ]);
});

it('render x-data correctly for detail row state', function () {
    $component = livewire(RulesToggleDetailTable::class);

    $perPage = data_get($component, 'payload.serverMemo.data.setUp.footer.perPage');
    $xData   = [];
    for ($i = 1; $i < $perPage + 1; $i++) {
        $xData[] = 'x-data="{ detailState: window.Livewire.find(\'' . $component->id() . '\').entangle(\'setUp.detail.state.' . $i . '\') }"';
    }

    $component->assertSeeHtmlInOrder($xData);
});
