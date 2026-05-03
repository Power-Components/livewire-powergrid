<?php

use PowerComponents\LivewirePowerGrid\Support\ThemeManager;
use PowerComponents\LivewirePowerGrid\Themes\{Tailwind, Theme};

it('resolves default value when no theme is bound', function () {
    expect(ThemeManager::theme('non.existent', 'default-value'))
        ->toBe('default-value');
});

it('resolves value from bound theme', function () {
    $theme = new class() extends Theme
    {
        public function struct(): array
        {
            return [
                'table' => [
                    'base' => 'bg-red-500',
                ],
            ];
        }
    };

    app()->instance('powergrid.theme', $theme);

    expect(ThemeManager::theme('table.base', 'default'))
        ->toBe('bg-red-500')
        ->and(ThemeManager::theme('non.existent', 'default'))
        ->toBe('default');
});

it('resolves default view when no theme is bound', function () {
    $tailwind = new Tailwind();

    expect(ThemeManager::view('table.header'))
        ->toBe($tailwind->resolveView('table.header'));
});

it('resolves view from bound theme', function () {
    $theme = new class() extends Theme
    {
        public function struct(): array
        {
            return [];
        }

        public function views(): array
        {
            return [
                'table.header' => 'custom-theme::table.header',
            ];
        }
    };

    app()->instance('powergrid.theme', $theme);

    expect(ThemeManager::view('table.header'))
        ->toBe('custom-theme::table.header');
});
