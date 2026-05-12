<?php

use Illuminate\View\View;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

it('shows the Powergrid default "no data" message', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-no-data';

        public function datasource()
        {
            return collect([]);
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtml('<span>No records found</span>');
});

it('shows a custom string message', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-no-data-string';

        public function datasource()
        {
            return collect([]);
        }

        public function noDataLabel(): string|View
        {
            return 'foo bar 1234';
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtml('<span>foo bar 1234</span>');
});

it('shows a custom view message', function () {
    view()->addLocation(__DIR__.'/../Concerns/Fixtures/views');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-no-data-view';

        public function datasource()
        {
            return collect([]);
        }

        public function noDataLabel(): string|View
        {
            return view('no-data');
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtml('<div><span class="custom">No Data Here!!!</span></div>');
});
