<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Tabs;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields};

uses()->group('tabs');

function rightAlignTabsComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'tabs-align';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'A']]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::tabs()->add('all', label: 'All')->right()->default('all'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [];
        }
    };
}

function alignDefaultTabsComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'tabs-align-default';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'A']]);
        }

        public function setUp(): array
        {
            return [PowerGrid::tabs()->add('all', label: 'All')->default('all')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [];
        }
    };
}

it('defaults the tabs alignment to center', function () {
    expect(Tabs::make()->align)->toBe('center');
});

it('sets alignment through left, center and right helpers', function () {
    expect(Tabs::make()->left()->align)->toBe('left')
        ->and(Tabs::make()->center()->align)->toBe('center')
        ->and(Tabs::make()->right()->align)->toBe('right');
});

it('falls back to center for an invalid alignment', function () {
    expect(Tabs::make()->align('sideways')->align)->toBe('center');
});

it('exposes the configured alignment through tabsAlign()', function () {
    $component = rightAlignTabsComponent();
    $component->setUp = ['tabs' => PowerGrid::tabs()->add('all', label: 'All')->right()];

    expect($component->tabsAlign())->toBe('right');
});

it('defaults the rendered tabs strip to center', function () {
    expect(Livewire::test(alignDefaultTabsComponent()::class)->html())
        ->toContain('pg-tabs flex w-full justify-center');
});

it('renders the tabs strip aligned per the builder', function () {
    expect(Livewire::test(rightAlignTabsComponent()::class)->html())
        ->toContain('pg-tabs flex w-full justify-end');
});
