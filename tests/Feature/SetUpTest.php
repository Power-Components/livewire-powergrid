<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('show includeViewOnTop/Bottom - Header', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-setup-header';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::header()
                    ->includeViewOnTop('livewire-powergrid::tests.header-top')
                    ->includeViewOnBottom('livewire-powergrid::tests.header-bottom'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtmlInOrder([
            '<div>Included By Header Top</div>',
            'Dish 1',
        ])
        ->assertSeeHtml('<div>Included By Header Bottom</div>');
});

it('show includeViewOnTop/Bottom - Footer', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-setup-footer';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::footer()
                    ->includeViewOnTop('livewire-powergrid::tests.footer-top')
                    ->includeViewOnBottom('livewire-powergrid::tests.footer-bottom'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtmlInOrder([
            'Dish 1',
            '<div>Included By Footer Top</div>',
            '<div>Included By Footer Bottom</div>',
        ]);
});
