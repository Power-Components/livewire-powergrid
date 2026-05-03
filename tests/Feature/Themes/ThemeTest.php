<?php

use PowerComponents\LivewirePowerGrid\Themes\{Tailwind, Theme};

it('resolves tokens properly', function () {
    $theme = new class() extends Theme
    {
        public function struct(): array
        {
            return [
                'table' => [
                    'base' => 'bg-blue-500',
                    'header' => 'text-white',
                ],
            ];
        }
    };

    expect($theme->resolveTokens())
        ->toBe([
            'table' => [
                'base' => 'bg-blue-500',
                'header' => 'text-white',
            ],
        ]);
});

it('gets a specific token', function () {
    $theme = new class() extends Theme
    {
        public function struct(): array
        {
            return [
                'table' => [
                    'base' => 'bg-blue-500',
                ],
            ];
        }
    };

    expect($theme->get('table.base'))
        ->toBe('bg-blue-500')
        ->and($theme->get('table.non_existent', 'default'))
        ->toBe('default');
});

it('merges tokens with overrides', function () {
    $theme = new class() extends Theme
    {
        public function struct(): array
        {
            return [
                'table' => [
                    'base' => 'bg-blue-500',
                    'row' => 'bg-white',
                ],
            ];
        }
    };

    $theme->merge([
        'table' => [
            'base' => 'bg-red-500',
            'new_key' => 'text-black',
        ],
    ]);

    expect($theme->get('table.base'))
        ->toBe('bg-red-500')
        ->and($theme->get('table.row'))
        ->toBe('bg-white')
        ->and($theme->get('table.new_key'))
        ->toBe('text-black');
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
                'table.base' => 'custom-theme::table.base',
            ];
        }
    };

    expect($theme->resolveView('table.base'))
        ->toBe('custom-theme::table.base');
});

it('falls back to tailwind view when theme does not have the view', function () {
    $theme = new class() extends Theme
    {
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
