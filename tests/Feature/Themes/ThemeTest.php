<?php

use PowerComponents\LivewirePowerGrid\PowerGridManager;
use PowerComponents\LivewirePowerGrid\Themes\{ArrayTheme, DaisyUI, Flux, Tailwind, Theme};

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
        ->and($theme->get('table.layout.container'))->toBe('overflow-x-auto')
        ->and($theme->get('table.layout.th'))->toBe('')
        ->and($theme->get('table.checkbox.input'))->toBe('checkbox');
});

it('flux provides correct tokens using ThemeBuilder', function () {
    $theme = new Flux();

    expect($theme->get('name'))->toBe('flux')
        ->and($theme->get('table.layout.table'))->toBe('min-w-full')
        ->and($theme->get('table.layout.container'))->toBe('overflow-x-auto relative border-t border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900');
});

it('applies no-code theme_overrides from config with highest precedence', function () {
    config(['livewire-powergrid.theme_overrides' => [
        'table' => ['layout' => ['th' => 'sentinel-th-class']],
    ]]);

    $theme = new Tailwind();

    expect($theme->get('table.layout.th'))->toBe('sentinel-th-class')
        // untouched tokens still come from the theme
        ->and($theme->get('table.layout.container'))->toBe('overflow-x-auto relative border-t border-zinc-200 dark:bg-zinc-700 dark:border-zinc-600');
});

it('leaves tokens unchanged when theme_overrides is empty', function () {
    config(['livewire-powergrid.theme_overrides' => []]);

    expect((new Tailwind())->get('table.layout.th'))
        ->toBe('font-extrabold px-3 py-3 text-left text-xs text-zinc-700 tracking-wider whitespace-nowrap dark:text-zinc-300');
});

it('resolves a theme by registered name or FQCN', function () {
    expect(PowerGridManager::resolveThemeClass('tailwind'))->toBe(Tailwind::class)
        ->and(PowerGridManager::resolveThemeClass('daisyui'))->toBe(DaisyUI::class)
        ->and(PowerGridManager::resolveThemeClass('flux'))->toBe(Flux::class)
        ->and(PowerGridManager::resolveThemeClass(Tailwind::class))->toBe(Tailwind::class);
});

it('registers a custom theme under a name', function () {
    $custom = new class() extends Theme
    {
        public function struct(): array
        {
            return [];
        }
    };

    PowerGridManager::registerTheme('my-custom', $custom::class);

    expect(PowerGridManager::resolveThemeClass('my-custom'))->toBe($custom::class);

    PowerGridManager::$themes = PowerGridManager::DEFAULT_THEMES;
});

it('builds a theme from a plain token array inheriting the parent', function () {
    $theme = ArrayTheme::fromArray([
        'footer' => ['pagination' => ['item' => 'btn btn-sm']],
    ], Tailwind::class, 'array-demo');

    $tailwind = new Tailwind();

    expect($theme->name())->toBe('array-demo')
        ->and($theme->get('footer.pagination.item'))->toBe('btn btn-sm')
        // everything else falls back to Tailwind
        ->and($theme->get('table.layout.container'))->toBe($tailwind->get('table.layout.container'));
});

it('resolves tabs as a theme-aware token group across the three themes', function () {
    $tailwind = new Tailwind();
    $daisyui = new DaisyUI();
    $flux = new Flux();

    // tailwind + daisyui share the token-driven base blade; flux keeps its own <flux:*> view
    expect($tailwind->resolveView('tabs'))->toBe('livewire-powergrid::components.themes.tailwind.tabs')
        ->and($daisyui->resolveView('tabs'))->toBe('livewire-powergrid::components.themes.tailwind.tabs')
        ->and($flux->resolveView('tabs'))->toBe('powergrid-plugins::Tabs.themes.flux')
        // each theme styles tabs through its own tokens
        ->and($tailwind->get('tabs.tab_active'))->toBe('bg-gray-100 text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white')
        ->and($daisyui->get('tabs.list'))->toBe('tabs tabs-box')
        ->and($daisyui->get('tabs.tab'))->toBe('tab gap-2')
        ->and($daisyui->get('tabs.tab_active'))->toBe('tab-active')
        ->and($flux->get('tabs.badge_active'))->toBe('bg-accent text-accent-foreground');
});

it('daisyui ships zero blades and inherits base views while keeping its own classes', function () {
    $daisyui = new DaisyUI();
    $tailwind = new Tailwind();

    // no daisyui blade folder — every override resolves to the Tailwind base view
    expect($daisyui->resolveView('header.search'))->toBe($tailwind->resolveView('header.search'))
        ->and($daisyui->resolveView('pagination'))->toBe($tailwind->resolveView('pagination'))
        ->and($daisyui->resolveView('header.soft-deletes'))->toBe($tailwind->resolveView('header.soft-deletes'))
        ->and($daisyui->resolveView('header.toggle-columns'))->toBe($tailwind->resolveView('header.toggle-columns'))
        // ...but the daisyUI look comes from tokens consumed by that shared base blade
        ->and($daisyui->get('pagination.item'))->toBe('btn btn-sm join-item')
        ->and($daisyui->get('pagination.item_active'))->toBe('btn btn-sm join-item btn-primary')
        ->and($daisyui->get('header.toggle_columns.item_label'))->toBe('text-sm text-base-content')
        ->and($daisyui->get('table.layout.table'))->toBe('table table-zebra')
        ->and($daisyui->get('table.layout.container'))->toBe('overflow-x-auto')
        ->and($daisyui->get('table.layout.th'))->toBe('')
        ->and($daisyui->get('table.layout.thead'))->toBe('')
        ->and($daisyui->get('table.checkbox.input'))->toBe('checkbox');

    expect(is_dir(__DIR__.'/../../../resources/views/components/themes/daisyui'))->toBeFalse();
});

it('auto-merges section methods and overrides only the given section', function () {
    $theme = new class() extends Theme
    {
        protected ?string $parentTheme = Tailwind::class;

        public function struct(): array
        {
            return [];
        }

        /** @return array<string, mixed> */
        public function footer(): array
        {
            return ['footer' => ['pagination' => 'my-pagination']];
        }
    };

    $tailwind = new Tailwind();

    // the section method wins for footer
    expect($theme->get('footer.pagination'))->toBe('my-pagination')
        // header (not overridden) is inherited from Tailwind
        ->and($theme->get('header.search_box.icon'))->toBe($tailwind->get('header.search_box.icon'));
});
