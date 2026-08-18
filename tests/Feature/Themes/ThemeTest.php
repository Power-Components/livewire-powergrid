<?php

use PowerComponents\LivewirePowerGrid\Themes\{DaisyUI, Flux, Tailwind, Theme};

it('resolves tokens properly', function () {
    $theme = new class() extends Theme
    {
        protected ?string $parentTheme = null;

        public function struct(): array
        {
            return [
                'table' => [
                    'table' => 'bg-blue-500',
                    'header' => 'text-white',
                ],
            ];
        }
    };

    expect($theme->resolveTokens())
        ->toBe([
            'table' => [
                'table' => 'bg-blue-500',
                'header' => 'text-white',
            ],
        ]);
});

it('gets a specific token', function () {
    $theme = new class() extends Theme
    {
        protected ?string $parentTheme = null;

        public function struct(): array
        {
            return [
                'table' => [
                    'table' => 'bg-blue-500',
                ],
            ];
        }
    };

    expect($theme->get('table.table'))
        ->toBe('bg-blue-500')
        ->and($theme->get('table.non_existent', 'default'))
        ->toBe('default');
});

it('merges tokens with overrides', function () {
    $theme = new class() extends Theme
    {
        protected ?string $parentTheme = null;

        public function struct(): array
        {
            return [
                'table' => [
                    'table' => 'bg-blue-500',
                    'row' => 'bg-white',
                ],
            ];
        }
    };

    $theme->merge([
        'table' => [
            'table' => 'bg-red-500',
            'new_key' => 'text-black',
        ],
    ]);

    expect($theme->get('table.table'))
        ->toBe('bg-red-500')
        ->and($theme->get('table.row'))
        ->toBe('bg-white')
        ->and($theme->get('table.new_key'))
        ->toBe('text-black');
});

it('inherits tokens from parent theme', function () {
    $theme = new class() extends Theme
    {
        protected ?string $parentTheme = Tailwind::class;

        public function struct(): array
        {
            return [
                'table' => [
                    'layout' => [
                        'table' => 'overridden-base',
                    ],
                ],
            ];
        }
    };

    $tailwind = new Tailwind();

    expect($theme->get('table.layout.table'))->toBe('overridden-base')
        ->and($theme->get('table.layout.container'))->toBe($tailwind->get('table.layout.container'));
});

it('resolves specific view from theme', function () {
    $theme = new class() extends Theme
    {
        public function struct(): array
        {
            return [];
        }

        public function views(): array
        {
            return [
                'table.table' => 'custom-theme::table.table',
            ];
        }
    };

    expect($theme->resolveView('table.table'))
        ->toBe('custom-theme::table.table');
});

it('falls back to tailwind view when theme does not have the view', function () {
    $theme = new class() extends Theme
    {
        protected ?string $parentTheme = Tailwind::class;

        public function struct(): array
        {
            return [];
        }

        public function views(): array
        {
            return [];
        }
    };

    $tailwind = new Tailwind();

    expect($theme->resolveView('table.header'))
        ->toBe($tailwind->resolveView('table.header'));
});

it('can be instantiated with make', function () {
    $themeClass = new class() extends Theme
    {
        public function struct(): array
        {
            return [];
        }
    };

    $instance = $themeClass::make();

    expect($instance)->toBeInstanceOf(Theme::class);
});

it('daisyui provides correct tokens using ThemeBuilder', function () {
    $theme = new DaisyUI();

    expect($theme->get('table.layout.table'))->toBe('table table-zebra')
        ->and($theme->get('table.layout.container'))->toBe('overflow-x-auto rounded-t-lg relative border-x border-t border-base-300');
});

it('flux provides correct tokens using ThemeBuilder', function () {
    $theme = new Flux();

    expect($theme->get('name'))->toBe('flux')
        ->and($theme->get('table.layout.table'))->toBe('min-w-full')
        ->and($theme->get('table.layout.container'))->toBe('overflow-x-auto rounded-t-lg relative border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900');
});
