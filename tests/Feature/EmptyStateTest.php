<?php

use Illuminate\View\View;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

it('shows the PowerGrid default empty state message', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-empty-state';

        public function datasource()
        {
            return collect([]);
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtml('<span>No records found</span>');
});

it('shows a custom string message via the deprecated noDataLabel()', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-empty-state-deprecated-string';

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

it('shows a custom message via renderEmptyState()', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-empty-state-string';

        public function datasource()
        {
            return collect([]);
        }

        public function renderEmptyState(): string|View
        {
            return 'empty state 5678';
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtml('<span>empty state 5678</span>');
});

it('shows a custom view message via the deprecated noDataLabel()', function () {
    view()->addLocation(__DIR__.'/../Concerns/Fixtures/views');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-empty-state-deprecated-view';

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
