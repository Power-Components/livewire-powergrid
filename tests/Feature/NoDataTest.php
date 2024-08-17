<?php

use Illuminate\View\View;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\NoDataCollectionTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$componentCustomMessage = new class () extends NoDataCollectionTable {
    public function noDataLabel(): string|View
    {
        return 'foo bar 1234';
    }
};

$componentCustomView = new class () extends NoDataCollectionTable {
    public function noDataLabel(): string|View
    {
        return view('no-data');
    }
};

it('shows the Powergrid default "no data" message', function (string $theme) {
    livewire(NoDataCollectionTable::class)
        ->call('setTestThemeClass', $theme)
        ->assertSeeHtml('<span>No records found</span>');
})->with([\PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class, \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]);

it('shows a custom string message', function ($component, $theme) {
    livewire($component)
           ->call('setTestThemeClass', $theme)
           ->assertSeeHtml('<span>foo bar 1234</span>');
})->with(['string' => [$componentCustomMessage::class]])
->with([\PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class, \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]);

it('show a view', function ($component, $theme) {
    $this->app['view']->addLocation(fixturePath('views'));

    livewire($component)
           ->call('setTestThemeClass', $theme)
           ->assertSeeHtml('<div><span class="custom">No Data Here!!!</span></div>');
})->with(['view' => [$componentCustomView::class]])
->with([\PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class, \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]);
