<?php

use Livewire\Attributes\On;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Themes\DaisyUI;
use PowerComponents\Turbine\{Button, Column};

describe('renderHeaderActions – HTML structure', function () {
    it('renders the buttons returned by header()', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'header-btn-default';

            public function datasource()
            {
                return collect([['id' => 1, 'name' => 'Alpha']]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name')];
            }

            public function header(): array
            {
                return [Button::add('bulk')->slot('Bulk')];
            }
        };

        Livewire::test($component::class)
            ->assertOk()
            ->assertSeeHtml('<button')
            ->assertSee('Bulk');
    });

    it('renders custom class and dispatch (wire:click) attributes', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'header-btn-attributes';

            public function datasource()
            {
                return collect([['id' => 1, 'name' => 'Alpha']]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name')];
            }

            public function header(): array
            {
                return [
                    Button::add('bulk')
                        ->slot('Bulk')
                        ->class('bulk-header-btn')
                        ->dispatch('bulkDelete', []),
                ];
            }
        };

        $html = Livewire::test($component::class)->html();

        expect($html)
            ->toContain('class="bulk-header-btn"')
            ->toContain('wire:click')
            ->toContain('bulkDelete');
    });

    it('renders an <a> tag when tag() is set to "a"', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'header-btn-anchor';

            public function datasource()
            {
                return collect([['id' => 1, 'name' => 'Alpha']]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name')];
            }

            public function header(): array
            {
                return [Button::add('link')->tag('a')->slot('Go')];
            }
        };

        Livewire::test($component::class)
            ->assertSeeHtml('<a ')
            ->assertSee('Go');
    });

    it('does not render anything when header() is empty', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'header-empty';

            public function datasource()
            {
                return collect([['id' => 1, 'name' => 'Alpha']]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name')];
            }
        };

        expect(Livewire::test($component::class)->instance()->renderHeaderActions())->toBe('');
    });
});

describe('renderHeaderActions – can', function () {
    it('hides the button when can is false', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'header-can-false';

            public function datasource()
            {
                return collect([['id' => 1, 'name' => 'Alpha']]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name')];
            }

            public function header(): array
            {
                return [Button::add('bulk')->slot('SecretBulk')->can(false)];
            }
        };

        Livewire::test($component::class)->assertDontSee('SecretBulk');
    });

    it('shows the button when can is true', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'header-can-true';

            public function datasource()
            {
                return collect([['id' => 1, 'name' => 'Alpha']]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name')];
            }

            public function header(): array
            {
                return [Button::add('bulk')->slot('VisibleBulk')->can(true)];
            }
        };

        Livewire::test($component::class)->assertSee('VisibleBulk');
    });
});

describe('renderHeaderActions – icon graceful degradation', function () {
    it('does not throw when the icon component does not exist', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'header-icon-invalid';

            public function datasource()
            {
                return collect([['id' => 1, 'name' => 'Alpha']]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name')];
            }

            public function header(): array
            {
                return [
                    Button::add('bulk')
                        ->slot('Bulk')
                        ->icon('non-existent-icon-xyz-abc', ['class' => 'size-4']),
                ];
            }
        };

        Livewire::test($component::class)
            ->assertOk()
            ->assertSee('Bulk');
    });
});

describe('renderHeaderActions – theme independence', function () {
    it('renders header buttons under the DaisyUI theme', function () {
        app()->instance('powergrid.theme', new DaisyUI());

        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'header-daisyui';

            public function datasource()
            {
                return collect([['id' => 1, 'name' => 'Alpha']]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name')];
            }

            public function header(): array
            {
                return [Button::add('bulk')->slot('DaisyBulk')];
            }
        };

        Livewire::test($component::class)
            ->assertOk()
            ->assertSee('DaisyBulk');
    });
});

it('re-queries after a header action unless the handler calls skipGridRender', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'header-action-skip-render';

        public static int $queries = 0;

        public function setUp(): array
        {
            $this->showCheckBox();

            return [];
        }

        public function datasource()
        {
            self::$queries++;

            return collect([['id' => 1, 'name' => 'Pastel']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Id', 'id'), Column::make('Name', 'name')];
        }

        public function header(): array
        {
            return [
                Button::add('copy')->slot('Copy')->dispatch('copy', []),
                Button::add('delete')->slot('Delete')->dispatch('delete', []),
            ];
        }

        #[On('copy')]
        public function copy(): void
        {
            $this->skipGridRender();
        }

        #[On('delete')]
        public function delete(): void {}
    };

    $test = Livewire::test($component::class)->set('checkboxValues', ['1']);
    $before = $component::$queries;

    $test->dispatch('copy');

    expect($component::$queries)->toBe($before)
        ->and($test->get('checkboxValues'))->toBe(['1']);

    $test->dispatch('delete');

    expect($component::$queries)->toBeGreaterThan($before);
});
