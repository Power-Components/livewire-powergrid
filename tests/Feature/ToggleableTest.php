<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Themes\{DaisyUI, Flux, Tailwind};

describe('toggleable – switch rendering', function () {
    it('renders the interactive switch when hasPermission is true', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'toggleable-on';

            public function datasource()
            {
                return collect([['id' => 1, 'status' => 1], ['id' => 2, 'status' => 0]]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('status');
            }

            public function columns(): array
            {
                return [
                    Column::make('Id', 'id'),
                    Column::make('Status', 'status')
                        ->toggleable(hasPermission: true, trueLabel: 'Active', falseLabel: 'Inactive'),
                ];
            }
        };

        $html = Livewire::test($component::class)->html();

        expect($html)
            ->toContain('x-data="pgToggleable')
            ->toContain('role="switch"');
    });

    it('drives the on/off color from theme tokens, dark-aware, with no shared CSS', function () {
        // Regression: the switch must not depend on the custom "pg-secondary"
        // Tailwind color, nor on any color baked into shared CSS. All four colors
        // (light/dark on/off) come from the active theme's toggleable tokens and
        // are applied as per-element CSS custom properties.
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'toggleable-color';

            public function datasource()
            {
                return collect([['id' => 1, 'status' => 1]]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('status');
            }

            public function columns(): array
            {
                return [
                    Column::make('Id', 'id'),
                    Column::make('Status', 'status')
                        ->toggleable(hasPermission: true, trueLabel: 'Active', falseLabel: 'Inactive'),
                ];
            }
        };

        $html = Livewire::test($component::class)->html();

        // Default theme is Tailwind: accent on, zinc-200 off (light), zinc-600 off (dark),
        // knob uses the accent-foreground so it contrasts with the "on" track.
        expect($html)
            ->toContain('--pg-toggle-on-light: var(--color-accent, #16a34a)')
            ->toContain('--pg-toggle-off-light: var(--color-zinc-200, #e4e4e7)')
            ->toContain('--pg-toggle-off-dark: var(--color-zinc-600, #52525b)')
            ->toContain('--pg-toggle-knob-on: var(--color-accent-foreground, #ffffff)')
            ->toContain('.dark .pg-toggleable-switch')
            ->not->toContain('pg-secondary');
    });
});

describe('toggleable – theme-driven colors', function () {
    it('each theme exposes its own on/off color tokens', function () {
        expect((new Tailwind())->resolveTokens()['toggleable'])
            ->toMatchArray([
                'color_on' => 'var(--color-accent, #16a34a)',
                'color_off' => 'var(--color-zinc-200, #e4e4e7)',
                'color_on_dark' => 'var(--color-accent, #16a34a)',
                'color_off_dark' => 'var(--color-zinc-600, #52525b)',
                'knob_on' => 'var(--color-accent-foreground, #ffffff)',
            ]);

        expect((new Flux())->resolveTokens()['toggleable'])
            ->toMatchArray([
                'color_on' => 'var(--color-accent, #4f46e5)',
                'color_off' => 'var(--color-zinc-200, #e4e4e7)',
                'color_on_dark' => 'var(--color-accent, #4f46e5)',
                'color_off_dark' => 'var(--color-zinc-700, #3f3f46)',
                'knob_on' => 'var(--color-accent-foreground, #ffffff)',
            ]);

        expect((new DaisyUI())->resolveTokens()['toggleable'])
            ->toMatchArray([
                'color_on' => 'var(--color-primary, oklch(0.45 0.24 277))',
                'color_off' => 'var(--color-base-300, #d1d5db)',
                'color_on_dark' => 'var(--color-primary, oklch(0.45 0.24 277))',
                'color_off_dark' => 'var(--color-base-300, #d1d5db)',
                'knob_on' => 'var(--color-primary-content, #ffffff)',
            ]);
    });
});

describe('toggleable – label fallback', function () {
    it('renders the true/false labels as a badge when hasPermission is false', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $tableName = 'toggleable-off';

            public function datasource()
            {
                return collect([['id' => 1, 'status' => 1], ['id' => 2, 'status' => 0]]);
            }

            public function fields(): PowerGridFields
            {
                return PowerGrid::fields()->add('id')->add('status');
            }

            public function columns(): array
            {
                return [
                    Column::make('Id', 'id'),
                    Column::make('Status', 'status')
                        ->toggleable(hasPermission: false, trueLabel: 'Active', falseLabel: 'Inactive'),
                ];
            }
        };

        $html = Livewire::test($component::class)->html();

        expect($html)
            ->not->toContain('x-data="pgToggleable')
            ->toContain('Active')     // row with status = 1
            ->toContain('Inactive');  // row with status = 0
    });
});
