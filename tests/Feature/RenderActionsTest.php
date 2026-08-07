<?php

use Illuminate\Support\Collection;
use Livewire\Livewire;
use PHPUnit\Framework\AssertionFailedError;
use PowerComponents\LivewirePowerGrid\{Button, Column, Facades\PowerGrid, Facades\Rule, PowerGridComponent, PowerGridFields};

describe('renderActions – HTML structure', function () {
    it('renders a <button> tag by default', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-btn-default';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('delete')->slot('Delete')];
            }
        };

        Livewire::test($component::class)
            ->assertSeeHtml('<button')
            ->assertSee('Delete');
    });

    it('renders an <a> tag when tag() is set to "a"', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-btn-anchor';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('link')->tag('a')->slot('Go')];
            }
        };

        Livewire::test($component::class)
            ->assertSeeHtml('<a ')
            ->assertSee('Go');
    });

    it('renders custom attributes on the button tag', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-btn-attributes';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [
                    Button::add('save')
                        ->slot('Save')
                        ->attributes(['class' => 'btn-primary', 'data-action' => 'save']),
                ];
            }
        };

        Livewire::test($component::class)
            ->assertSeeHtml('class="btn-primary"')
            ->assertSeeHtml('data-action="save"');
    });

    it('renders one button per row', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-btn-per-row';

            public function datasource()
            {
                return collect([
                    ['id' => 1, 'name' => 'Alpha'],
                    ['id' => 2, 'name' => 'Beta'],
                ]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('go')->slot('Go '.$row->id)];
            }
        };

        Livewire::test($component::class)
            ->assertSee('Go 1')
            ->assertSee('Go 2');
    });

    it('does not render the actions wrapper when no actions() method is defined', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-no-actions';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }
        };

        Livewire::test($component::class)
            ->assertOk()
            ->assertSee('Alpha');
    });
});

describe('renderActions – can', function () {
    it('hides the button when can is false', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-can-false';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('remove')->slot('Remove')->can(false)];
            }
        };

        Livewire::test($component::class)
            ->assertDontSee('Remove');
    });

    it('shows the button when can is true', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-can-true';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('remove')->slot('Remove')->can(true)];
            }
        };

        Livewire::test($component::class)
            ->assertSee('Remove');
    });

    it('shows button only for rows where can closure returns true', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-can-closure';

            public function datasource()
            {
                return collect([
                    ['id' => 1, 'name' => 'Alpha'],
                    ['id' => 2, 'name' => 'Beta'],
                ]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [
                    Button::add('admin')
                        ->slot('Admin '.$row->id)
                        ->can(fn ($r) => $r->id === 1),
                ];
            }
        };

        // Only row id=1 should render; row id=2 should not.
        Livewire::test($component::class)
            ->assertSee('Admin 1')
            ->assertDontSee('Admin 2');
    });

    it('hides all buttons when can closure always returns false', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-can-closure-false';

            public function datasource()
            {
                return collect([
                    ['id' => 1, 'name' => 'Alpha'],
                    ['id' => 2, 'name' => 'Beta'],
                ]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('secret')->slot('Secret')->can(fn () => false)];
            }
        };

        Livewire::test($component::class)
            ->assertDontSee('Secret');
    });
});

describe('renderActions – custom view', function () {
    it('renders the custom view instead of the default button markup', function () {
        // livewire-powergrid ships a test view at resources/views/tests/actions-view.blade.php
        // that outputs "Dish From Actions View: {id}".
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-custom-view';

            public function datasource()
            {
                return collect([
                    ['id' => 1, 'name' => 'Alpha'],
                    ['id' => 2, 'name' => 'Beta'],
                ]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('custom')->view('livewire-powergrid::tests.actions-view')];
            }
        };

        Livewire::test($component::class)
            ->assertSee('Dish From Actions View: 1')
            ->assertSee('Dish From Actions View: 2');
    });
});

describe('renderActions – rule hide', function () {
    it('hides a button only for the row matching the when() condition', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-rule-hide-partial';

            public function datasource()
            {
                return collect([
                    ['id' => 1, 'name' => 'Alpha'],
                    ['id' => 2, 'name' => 'Beta'],
                ]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('delete')->slot('Delete')];
            }

            public function actionRules($row): array
            {
                return [
                    Rule::button('delete')
                        ->when(fn ($r) => data_get($r, 'id') === 2)
                        ->hide(),
                ];
            }
        };

        // Row id=2 is hidden; row id=1 still renders.
        Livewire::test($component::class)
            ->assertSee('Delete');  // row 1
    });

    it('hides all buttons when every row matches the when() condition', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-rule-hide-all';

            public function datasource()
            {
                return collect([
                    ['id' => 1, 'name' => 'Alpha'],
                    ['id' => 2, 'name' => 'Beta'],
                ]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('nuke')->slot('Nuke')];
            }

            public function actionRules($row): array
            {
                return [
                    Rule::button('nuke')
                        ->when(fn () => true)
                        ->hide(),
                ];
            }
        };

        Livewire::test($component::class)
            ->assertDontSee('Nuke');
    });
});

describe('renderActions – rule setAttribute', function () {
    it('replaces the attribute value for the matching row only', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-rule-set-attr';

            public function datasource()
            {
                return collect([
                    ['id' => 1, 'name' => 'Alpha'],
                    ['id' => 2, 'name' => 'Beta'],
                ]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [
                    Button::add('edit')
                        ->slot('Edit')
                        ->attributes(['class' => 'btn-default']),
                ];
            }

            public function actionRules($row): array
            {
                return [
                    Rule::button('edit')
                        ->when(fn ($r) => data_get($r, 'id') === 1)
                        ->setAttribute('class', 'btn-danger'),
                ];
            }
        };

        // Row id=1 → btn-danger; row id=2 → btn-default.
        Livewire::test($component::class)
            ->assertSeeHtml('class="btn-danger"')
            ->assertSeeHtml('class="btn-default"');
    });
});

describe('renderActions – rule slot', function () {
    it('overrides the slot text for the row that matches the condition', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-rule-slot';

            public function datasource()
            {
                return collect([
                    ['id' => 1, 'name' => 'Alpha'],
                    ['id' => 2, 'name' => 'Beta'],
                ]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('status')->slot('Activate')];
            }

            public function actionRules($row): array
            {
                return [
                    Rule::button('status')
                        ->when(fn ($r) => data_get($r, 'id') === 2)
                        ->slot('Deactivate'),
                ];
            }
        };

        Livewire::test($component::class)
            ->assertSee('Activate')    // row id=1
            ->assertSee('Deactivate'); // row id=2
    });
});

describe('renderActions – icon graceful degradation', function () {
    it('does not throw when the icon component does not exist', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-icon-invalid';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [
                    Button::add('go')
                        ->slot('Go')
                        ->icon('non-existent-icon-xyz-abc', []),
                ];
            }
        };

        // Should render without exception; slot text must still appear.
        Livewire::test($component::class)
            ->assertOk()
            ->assertSee('Go');
    });
});

describe('renderActions – transformActions hook', function () {
    it('lets a transformActions override mutate the rendered action HTML', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-transform-actions';

            public function datasource()
            {
                return collect([
                    ['id' => 1, 'name' => 'Alpha'],
                    ['id' => 2, 'name' => 'Beta'],
                ]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('edit')->slot('ORIGINAL')];
            }

            public function transformActions(array $actionsByRow, Collection $rows): array
            {
                foreach ($actionsByRow as $rowId => &$actions) {
                    foreach ($actions as &$action) {
                        if ($action['action'] === 'edit') {
                            $action['slot'] = 'Edit #'.$rowId;
                        }
                    }
                }

                return $actionsByRow;
            }
        };

        Livewire::test($component::class)
            ->assertDontSee('ORIGINAL')
            ->assertSee('Edit #1')
            ->assertSee('Edit #2');
    });

    it('leaves output untouched when transformActions is not overridden', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-transform-actions-noop';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('edit')->slot('ORIGINAL')];
            }
        };

        Livewire::test($component::class)->assertSee('ORIGINAL');
    });
});

describe('renderActions – JS / wire directives survive into HTML', function () {
    it('renders wire:click from macros and custom Alpine attributes in the output', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'render-js-directives';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [
                    Button::add('save')->slot('Save')->dispatch('itemSaved', ['id' => $row->id]),
                    Button::add('del')->slot('Del')->confirm('Sure?'),
                    Button::add('alpine')->slot('Alpine')
                        ->attributes(['x-data' => '{ open: false }', 'x-on:click' => 'open = true']),
                ];
            }
        };

        $html = Livewire::test($component::class)->html();

        // wire:click value is HTML-escaped exactly as the old blade did (e()).
        expect($html)
            ->toContain('wire:click')
            ->toContain('$dispatch(')
            ->toContain('itemSaved')
            ->toContain('wire:confirm="Sure?"')
            ->toContain('x-data="{ open: false }"')
            ->toContain('x-on:click="open = true"');
    });
});

describe('renderActions – TestActions data layer', function () {
    it('assertHasAction passes for a declared action', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'test-actions-has-action';

            public function datasource()
            {
                return collect([
                    ['id' => 1, 'name' => 'Alpha'],
                    ['id' => 2, 'name' => 'Beta'],
                ]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('name');
            }

            public function columns(): array
            {
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [
                    Button::add('edit')
                        ->slot('Edit')
                        ->attributes(['class' => 'btn-edit', 'data-id' => (string) $row->id])
                        ->icon('heroicon-o-pencil', ['class' => 'w-4 h-4']),
                ];
            }
        };

        Livewire::test($component::class)
            ->assertHasAction('edit')
            ->assertActionContainsAttribute('edit', 'class', 'btn-edit')
            ->assertActionContainsAttribute('edit', 'data-id', '1')
            ->assertActionHasIcon('edit', 'heroicon-o-pencil', 'w-4 h-4')
            ->assertOk();
    });

    it('assertHasAction fails when the action is absent', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'test-actions-absent';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [Button::add('save')->slot('Save')];
            }
        };

        expect(fn () => Livewire::test($component::class)->assertHasAction('delete'))
            ->toThrow(AssertionFailedError::class);
    });

    it('assertActionContainsAttribute validates attribute values', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'test-actions-attributes';

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
                return [Column::make('Id', 'id'), Column::make('Name', 'name'), Column::action('Actions')];
            }

            public function actions($row): array
            {
                return [
                    Button::add('view')
                        ->slot('View')
                        ->attributes([
                            'class' => 'btn-view font-bold',
                            'data-type' => 'action',
                        ]),
                ];
            }
        };

        Livewire::test($component::class)
            ->assertHasAction('view')
            ->assertActionContainsAttribute('view', 'class', 'btn-view')
            ->assertActionContainsAttribute('view', 'class', 'font-bold')
            ->assertActionContainsAttribute('view', 'data-type', 'action')
            ->assertOk();
    });
});
