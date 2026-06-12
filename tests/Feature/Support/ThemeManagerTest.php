<?php

use PowerComponents\LivewirePowerGrid\Support\ThemeManager;
use PowerComponents\LivewirePowerGrid\Themes\{Tailwind, Theme};

beforeEach(fn () => ThemeManager::clearCache());

it('resolves default value when no theme is bound', function () {
    app()->forgetInstance('powergrid.theme');
    app()->offsetUnset('powergrid.theme');

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

it('resolves view from tokens', function () {
    $theme = new class() extends Theme
    {
        public function struct(): array
        {
            return [
                'view_toggle_detail' => 'my-toggle-detail-view',
                'header' => [
                    'view_export' => 'my-export-view',
                ],
            ];
        }
    };

    app()->instance('powergrid.theme', $theme);

    expect(ThemeManager::view('toggle-detail'))->toBe('my-toggle-detail-view')
        ->and(ThemeManager::view('header.export'))->toBe('my-export-view');
});

it('resolves nested views from tokens', function () {
    $theme = new class() extends Theme
    {
        public function struct(): array
        {
            return [
                'table' => [
                    'view_row' => 'my-table-row-view',
                ],
            ];
        }
    };

    app()->instance('powergrid.theme', $theme);

    expect(ThemeManager::view('table.row'))->toBe('my-table-row-view');
});
