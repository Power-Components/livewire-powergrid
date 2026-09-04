# PowerGrid 7.x Upgrade Guide

This guide details the steps required to upgrade your application from **PowerGrid 6.x** to **PowerGrid 7.x**. The 7.x release introduces a major architectural cleanup, replacing legacy dependencies with a simplified theme builder, a modular plugin system, and server-side action rendering.

---

## 1. System & Package Requirements

Ensure your environment meets the new minimum versions:
*   **PHP:** `8.3` or higher (PHP 8.4+ required for Laravel 13)
*   **Laravel:** `12.x` or `13.x` (Laravel 11 is no longer supported)
*   **Livewire:** `4.x` or higher (Livewire 3.x is no longer supported)
*   **Tailwind CSS:** `4.x` or higher (if using Tailwind)

### Install Required Dependencies

PowerGrid 7.x requires new packages:

```bash
composer require power-components/partials
```

---

## 2. Removed Dependencies & Cleanup

The following legacy features and themes have been completely removed. You must search your codebase and remove any configurations, imports, or files referencing them:

1.  **Bootstrap 5 Theme:** The Bootstrap 5 PHP theme class (`src/Themes/Bootstrap5.php`) and its view templates have been deleted. Only **Tailwind**, **DaisyUI**, and **Flux** are supported natively.
2.  **Third-Party Select Libraries:** The CDN-based fallback for TomSelect/SlimSelect has been removed. These libraries are now **optional** — PowerGrid no longer imports them, so your build never fails when they are absent. To use a multi-select filter, install the library and expose it globally in your bundle:
    ```js
    // resources/js/app.js
    import TomSelect from 'tom-select' // npm i tom-select
    window.TomSelect = TomSelect

    // or, for SlimSelect (npm i slim-select)
    import SlimSelect from 'slim-select'
    window.SlimSelect = SlimSelect
    ```
    If the library is not exposed, the filter degrades gracefully and a console message explains what to install.
3.  **Lazy Loading ("Load More") API:**
    - `PowerGrid::lazy()` facade method has been removed
    - `LazyManager` trait removed from PowerGridComponent
    - Methods removed: `loadMore()`, `hasLazyEnabled()`, `getLazyKeys()`, `canLoadMore()`
    - No direct replacement; use standard pagination instead (see Section 7)
4.  **Pulse Logger Integration:** Performance tracking files (`PowerGridPerformanceData`, `PowerGridPerformanceRecorder`, `PerformanceCard`) have been removed.
5.  **Helper Functions Removed:**
    - `theme_style()` - Use `theme()` helper instead
    - `isBootstrap5()` - No direct replacement (check theme class instead)
    - `isTailwind()` - No direct replacement (check theme class instead)
6.  **Theme Class Methods:**
    - `apply()` method - Use `struct()` instead
    - `layout()` method - Repurposed in fluent builder
    - `root()` method - Removed
    - `$base` property - Removed
7.  **View Namespace Changed:**
    - Old: `components.frameworks.[theme]`
    - New: `components.themes.[theme]`

---

## 3. Helper Function Migration

PowerGrid 7.x changes how you access theme styles and utilities in Blade views and PHP code.

### theme_style() → theme()

**v6 (Old):**
```blade
<td class="{{ theme_style($theme, 'table.body.td') }}">
```

**v7 (New):**
```blade
<td class="{{ theme('table.layout.td') }}">
```

**Key Changes:**
- No longer requires `$theme` array parameter
- Directly accesses ThemeManager singleton
- Returns string directly (no array traversal needed)

### $theme Array No Longer Available

In v6, Blade views received a `$theme` array. In v7, this is removed.

**Migration:**
```blade
{{-- v6 --}}
@if($theme['name'] === 'tailwind')
    <div class="...">
@endif

{{-- v7 --}}
@if(app('powergrid.theme')->name() === 'tailwind')
    <div class="...">
@endif
```

### New Helper: theme_view()

```php
// Resolves view path from theme configuration
theme_view('header') // Returns: 'livewire-powergrid::components.themes.tailwind.header'
```

### Removed Theme Detection Helpers

```php
// v6 - These functions existed
isBootstrap5(); // ❌ Removed
isTailwind();   // ❌ Removed

// v7 - Check theme class instead
$theme = app('powergrid.theme');
$isTailwind = $theme instanceof \PowerComponents\LivewirePowerGrid\Themes\Tailwind;
```

---

## 4. Custom Theme Migration

In version 7.x, the theme architecture has been **completely rewritten**. This is not a simple key renaming - it's a fundamental architectural change.

### Understanding the Architectural Shift

**v6 Architecture (Legacy):**
- Used separate methods returning arrays: `table()`, `header()`, `checkbox()`, `searchBox()`, etc.
- Flat array structure with nested associative arrays
- No type hints or IDE support

**v7 Architecture (Current):**
- Uses a single `struct()` method returning a fluent `ThemeBuilder`
- Method chaining with typed closure parameters
- Nested structure with `checkbox`/`radio` under `table`, `searchBox` under `header`
- Separate methods remain for: `editable()`, `toggleable()`, `filter()`
- New sections: `layout`, `header`, expanded `searchBox`

### Step 1: Update Class Inheritance & Struct Signature

All custom themes must extend `PowerComponents\LivewirePowerGrid\Themes\Theme` and implement the `struct()` method that returns `Components\ThemeBuilder`:

```php
use PowerComponents\LivewirePowerGrid\Themes\Theme;
use PowerComponents\LivewirePowerGrid\Themes\Components;

class CustomTheme extends Theme
{
    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.themes.custom')
            // ... fluent builder chain
            ;
    }
}
```

> ⚠️ **Do not declare a `public string $name` property.** In v7 the name is derived
> automatically from the class basename (kebab-cased) by `Theme::name()`. A declared
> property is dead code and will not be used.

#### Shortcut used in real-world upgrades: inherit from the base Tailwind theme

If your v6 theme was a *branded Tailwind variant* (same structure, different classes),
you do **not** need to re-declare all 41 struct tokens. Point `parentTheme` at
`Tailwind::class`, override only the tokens that differ in `struct()`, and merge the
rest through `filter()` / `editable()`. Everything you skip is inherited:

```php
<?php

namespace App\Helpers;

use PowerComponents\LivewirePowerGrid\Themes\{Components, Tailwind, Theme};

class PowerGridTheme extends Theme
{
    // Every token not overridden here is inherited from the base Tailwind theme.
    protected ?string $parentTheme = Tailwind::class;

    public function struct(): Components\ThemeBuilder
    {
        // checkbox(), radio(), cols(), footer(), searchBox()... omitted = inherited
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.themes.tailwind')
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('p-3 align-middle sm:px-6 lg:px-8')
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('overflow-x-auto rounded-t-lg relative border-x border-t border-gray-200')
                    ->th('font-semibold px-3 py-3 text-left text-xs text-white whitespace-nowrap')
                    ->td('px-3 py-2 whitespace-nowrap')
                    // ...
                )
            );
    }

    public function filter(): array
    {
        // Only the filter tokens that differ; the rest come from Tailwind.
        return [
            'filter' => [
                'boolean' => [
                    'base'   => 'min-w-[5rem]',
                    'select' => '...',
                ],
                'input_text' => [
                    'input'  => '...',
                    'select' => '...',
                ],
            ],
        ];
    }
}
```

Tokens without a dedicated builder method (e.g. `table.body.td.actions_wrapper`,
the filter panel tokens `filter.dropdown.*` / `filter.flyout.*`, or the header
element tokens) can be added by overriding `resolveTokens()` and merging them into
the resolved set:

```php
public function resolveTokens(): array
{
    if (empty($this->tokens)) {
        $tokens = parent::resolveTokens();

        $this->tokens = array_replace_recursive($tokens, [
            'table' => [
                'body' => [
                    'td' => [
                        'actions_wrapper' => 'flex gap-2',
                    ],
                ],
            ],
            'filter' => [
                'dropdown' => [
                    'body' => 'px-4 py-3',
                ],
            ],
        ]);
    }

    return $this->tokens;
}
```

This is exactly how a large production app migrated its v6 theme with a fraction of
the full mapping work — see Section 4 Step 3 only when your theme diverges structurally
from Tailwind.

### Step 2: Understand the Component Classes

The fluent builder uses these typed component classes:

- `Components\ThemeBuilder` - Main builder (entry point)
- `Components\Layout` - For layout configurations (reused in multiple places)
- `Components\Header` - For header structure (NEW in v7)
- `Components\SearchBox` - For search box structure (expanded in v7)
- `Components\Table` - For table structure
- `Components\Body` - For body structure
- `Components\Tr` - For table row structure
- `Components\Checkbox` - For checkbox structure (now nested under table)
- `Components\Radio` - For radio structure (now nested under table)
- `Components\Cols` - For column structure
- `Components\Footer` - For footer structure

### Step 3: Translate Keys using Complete Mapping

⚠️ **IMPORTANT:** This is NOT a 1:1 key mapping. The structure has changed fundamentally. Below is the complete mapping showing the correct v7 architecture:

#### Layout (NEW IN V7)
| Legacy 6.x | New 7.x Fluent Builder | Notes |
| :--- | :--- | :--- |
| N/A | `->layout(fn (Components\Layout $layout) => $layout->wrapper('...')` | NEW - Set to empty or adapt |
| N/A | `->layout(fn (Components\Layout $layout) => $layout->outsideFilters('...')` | NEW - Set to empty or adapt |

#### Header Structure (NEW IN V7)
| Legacy 6.x | New 7.x Fluent Builder | Notes |
| :--- | :--- | :--- |
| N/A | `->header(fn (Components\Header $header) => $header->view('header')` | NEW - Usually 'header' |
| N/A | `->header()->layout()->container('...')` | NEW - Set based on framework |
| N/A | `->header()->layout()->subContainer('...')` | NEW - Set based on framework |
| N/A | `->header()->layout()->actionsContainer('...')` | NEW - Set based on framework |
| `table()['layout']['actions']` | `->header()->layout()->actions('...')` | MOVED from table to header |

#### SearchBox (RESTRUCTURED - now under header)
| Legacy 6.x | New 7.x Fluent Builder | Notes |
| :--- | :--- | :--- |
| N/A | `->header()->searchBox()->view('header.search')` | NEW - Usually 'header.search' |
| N/A | `->header()->searchBox()->container('...')` | NEW - Set based on framework |
| N/A | `->header()->searchBox()->relativeMain('...')` | NEW - Set based on framework |
| `searchBox()['input']` | `->header()->searchBox()->input('...')` | Now nested under header |
| N/A | `->header()->searchBox()->iconSearchWrapper('...')` | NEW - Wrapper for icon |
| N/A | `->header()->searchBox()->iconCloseWrapper('...')` | NEW - Wrapper for icon |
| `searchBox()['iconClose']` | `->header()->searchBox()->iconClose('...')` | Now nested under header |
| `searchBox()['iconSearch']` | `->header()->searchBox()->iconSearch('...')` | Now nested under header |

#### Table Layout (RESTRUCTURED)
| Legacy 6.x | New 7.x Fluent Builder | Notes |
| :--- | :--- | :--- |
| `table()['layout']['container']` | `->table()->layout()->container('...')` | |
| `table()['layout']['table']` | `->table()->layout()->table('...')` | |
| `table()['header']['thead']` | `->table()->layout()->thead('...')` | |
| `table()['header']['tr']` | `->table()->layout()->tr('...')` | |
| `table()['header']['th']` | `->table()->layout()->th('...')` | |
| `table()['header']['thAction']` | `->table()->layout()->thActions('...')` | Renamed to thActions |
| `table()['body']['tbody']` | `->table()->layout()->tbody('...')` | |
| `table()['body']['td']` | `->table()->layout()->td('...')` | |
| `table()['body']['tdActionsContainer']` | `->table()->layout()->tdActions('...')` | Renamed to tdActions |

#### Table Body Responsive (NEW IN V7)
| Legacy 6.x | New 7.x Fluent Builder | Notes |
| :--- | :--- | :--- |
| N/A | `->table()->body()->tr()->responsive('...')` | NEW - Set based on framework |
| N/A | `->table()->body()->tr()->responsiveToggleIcon('...')` | NEW - Icon for toggle |

#### Checkbox (NESTED under table in v7)
| Legacy 6.x | New 7.x Fluent Builder | Notes |
| :--- | :--- | :--- |
| `checkbox()['th']` | `->table()->checkbox()->th('...')` | Now nested under table |
| `checkbox()['base']` | `->table()->checkbox()->base('...')` | Now nested under table |
| `checkbox()['label']` | `->table()->checkbox()->label('...')` | Now nested under table |
| `checkbox()['input']` | `->table()->checkbox()->input('...')` | Now nested under table |

#### Radio (NESTED under table in v7)
| Legacy 6.x | New 7.x Fluent Builder | Notes |
| :--- | :--- | :--- |
| `radio()['th']` | `->table()->radio()->th('...')` | Now nested under table |
| `radio()['base']` | `->table()->radio()->base('...')` | Now nested under table |
| `radio()['label']` | `->table()->radio()->label('...')` | Now nested under table |
| `radio()['input']` | `->table()->radio()->input('...')` | Now nested under table |

#### Cols (UNCHANGED)
| Legacy 6.x | New 7.x Fluent Builder | Notes |
| :--- | :--- | :--- |
| `cols()['div']` | `->cols(fn (Components\Cols $cols) => $cols->div('...')` | |

#### Footer (RESTRUCTURED with nested layout)
| Legacy 6.x | New 7.x Fluent Builder | Notes |
| :--- | :--- | :--- |
| `footer()['view']` | `->footer()->view('footer')` | |
| `footer()['footer']` | `->footer()->layout()->container('...')` | Renamed & nested |
| `footer()['select']` | `->footer()->layout()->select('...')` | Now nested under layout |
| `footer()['footer_with_pagination']` | `->footer()->pagination('pagination')` | Renamed to pagination |

#### Editable (SEPARATE METHOD - not in struct())
| Legacy 6.x | New 7.x (Separate Method) | Notes |
| :--- | :--- | :--- |
| `editable()['view']` | `editable()->view('...')` | Still in separate method |
| N/A | `editable()->clickable('...')` | NEW in v7 |
| `editable()['input']` | `editable()->input('...')` | Still in separate method |
| N/A | `editable()->error('...')` | NEW in v7 |

#### Toggleable (SEPARATE METHOD - simplified in v7)
| Legacy 6.x | New 7.x (Separate Method) | Notes |
| :--- | :--- | :--- |
| `toggleable()['view']` | `toggleable()->view('...')` | Only view remains |
| `toggleable()['base']` | ❌ REMOVED | No longer configurable |
| `toggleable()['label']` | ❌ REMOVED | No longer configurable |
| `toggleable()['input']` | ❌ REMOVED | No longer configurable |
| `toggleable()['role']` | ❌ REMOVED | No longer configurable |

#### Filters (SEPARATE METHOD - not in struct())
| Legacy 6.x | New 7.x (Separate Method) | Notes |
| :--- | :--- | :--- |
| N/A | `filter()->label('...')` | NEW in v7 |
| `filterBoolean()['view']` | `filter.boolean.view` | Still in separate method |
| `filterBoolean()['base']` | `filter.boolean.base` | Still in separate method |
| `filterBoolean()['select']` | `filter.boolean.select` | Still in separate method |
| `filterDatePicker()['view']` | `filter.date_picker.view` | Still in separate method |
| `filterDatePicker()['base']` | `filter.date_picker.base` | Still in separate method |
| `filterDatePicker()['input']` | `filter.date_picker.input` | Still in separate method |
| `filterMultiSelect()['view']` | `filter.multi_select.view` | Still in separate method |
| `filterMultiSelect()['base']` | `filter.multi_select.base` | Still in separate method |
| `filterMultiSelect()['select']` | `filter.multi_select.select` | Still in separate method |
| `filterNumber()['view']` | `filter.number.view` | Still in separate method |
| N/A | `filter.number.base` | NEW - was missing in v6 |
| `filterNumber()['input']` | `filter.number.input` | Still in separate method |
| `filterSelect()['view']` | `filter.select.view` | Still in separate method |
| `filterSelect()['base']` | `filter.select.base` | Still in separate method |
| `filterSelect()['select']` | `filter.select.select` | Still in separate method |
| `filterInputText()['view']` | `filter.input_text.view` | Still in separate method |
| `filterInputText()['base']` | `filter.input_text.base` | Still in separate method |
| `filterInputText()['select']` | `filter.input_text.select` | Still in separate method |
| `filterInputText()['input']` | `filter.input_text.input` | Still in separate method |
| N/A | `filter.input` | NEW - global input styles |

#### V6 Keys Removed in V7
These v6 keys have no v7 equivalent and should be discarded:
- ❌ `table()['layout']['base']` - No longer used
- ❌ `table()['layout']['div']` - No longer used
- ❌ `table()['body']['tbodyEmpty']` - Removed
- ❌ `table()['body']['tdEmpty']` - Removed
- ❌ `table()['body']['tdSummarize']` - Removed
- ❌ `table()['body']['trSummarize']` - Removed
- ❌ `table()['body']['tdFilters']` - Removed
- ❌ `table()['body']['trFilters']` - Removed

### Step 4: Complete Migration Example

Here's a complete before/after example showing the architectural transformation:

#### Legacy 6.x Theme Class (Bootstrap5):
```php
<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Bootstrap5 extends Theme
{
    public function table(): array
    {
        return [
            'layout' => [
                'table' => 'table-hover table-striped',
                'container' => 'my-0',
                'actions' => 'btn-group',
            ],
            'header' => [
                'thead' => '',
                'tr' => '',
                'th' => 'fw-bold text-secondary',
                'thAction' => 'text-center',
            ],
            'body' => [
                'tbody' => '',
                'tr' => '',
                'td' => 'align-middle text-nowrap',
                'tdActionsContainer' => 'text-center',
            ],
        ];
    }

    public function checkbox(): array
    {
        return [
            'th' => 'fs-6 text-center',
            'base' => 'form-check',
            'label' => 'form-check-label',
            'input' => 'form-check-input',
        ];
    }

    public function searchBox(): array
    {
        return [
            'input' => 'form-control',
            'iconSearch' => 'bi bi-search',
            'iconClose' => 'bi bi-x',
        ];
    }

    public function footer(): array
    {
        return [
            'view' => $this->root().'.footer',
            'select' => 'form-select',
            'footer' => 'd-flex justify-content-between',
            'footer_with_pagination' => 'pagination',
        ];
    }
}
```

#### New 7.x Theme Class (Bootstrap5 Migrated):
```php
<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components;

class Bootstrap5 extends Theme
{
    public string $name = 'bootstrap5';

    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.themes.bootstrap5')

            // NEW: Top-level layout (add empty or adapt)
            ->layout(fn (Components\Layout $layout) => $layout
                ->wrapper('')
                ->outsideFilters('')
            )

            // NEW: Header structure
            ->header(fn (Components\Header $header) => $header
                ->view('header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('d-flex justify-content-between')
                    ->subContainer('')
                    ->actionsContainer('')
                    ->actions('btn-group')  // MOVED from v6 table.layout.actions
                )

                // SearchBox now nested under header
                ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                    ->view('header.search')
                    ->container('')
                    ->relativeMain('')
                    ->input('form-control')              // from v6 searchBox()
                    ->iconSearchWrapper('')
                    ->iconCloseWrapper('')
                    ->iconClose('bi bi-x')               // from v6 searchBox()
                    ->iconSearch('bi bi-search')         // from v6 searchBox()
                )
            )

            // Table structure with nested checkbox/radio
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('my-0')                  // from v6 table.layout.container
                    ->table('table-hover table-striped') // from v6 table.layout.table
                    ->thead('')                          // from v6 table.header.thead
                    ->tr('')                             // from v6 table.header.tr
                    ->th('fw-bold text-secondary')       // from v6 table.header.th
                    ->thActions('text-center')           // from v6 table.header.thAction
                    ->tbody('')                          // from v6 table.body.tbody
                    ->td('align-middle text-nowrap')     // from v6 table.body.td
                    ->tdActions('text-center')           // from v6 table.body.tdActionsContainer
                )

                // NEW: Body responsive structure
                ->body(fn (Components\Body $body) => $body
                    ->tr(fn (Components\Tr $tr) => $tr
                        ->responsive('')
                        ->responsiveToggleIcon('')
                    )
                )

                // Checkbox now nested under table
                ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox
                    ->th('fs-6 text-center')             // from v6 checkbox()
                    ->base('form-check')                 // from v6 checkbox()
                    ->label('form-check-label')          // from v6 checkbox()
                    ->input('form-check-input')          // from v6 checkbox()
                )

                // Radio now nested under table (adapt from checkbox if not in v6)
                ->radio(fn (Components\Radio $radio) => $radio
                    ->th('fs-6 text-center')
                    ->base('form-check')
                    ->label('form-check-label')
                    ->input('form-check-input')
                )
            )

            ->cols(fn (Components\Cols $cols) => $cols
                ->div('')                                // from v6 cols.div
            )

            // Footer with nested layout
            ->footer(fn (Components\Footer $footer) => $footer
                ->view('footer')                         // from v6 footer.view
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('d-flex justify-content-between')  // from v6 footer.footer
                    ->select('form-select')                        // from v6 footer.select
                )
                ->pagination('pagination')               // from v6 footer.footer_with_pagination
            );
    }

    // These remain as separate methods
    public function editable(): array
    {
        return [
            'editable' => (new Components\Component())
                ->view('livewire-powergrid::components.themes.bootstrap5.editable')
                ->clickable('cursor-pointer')            // NEW in v7
                ->input('form-control')
                ->error('is-invalid')                    // NEW in v7
                ->toArray(),
        ];
    }

    public function toggleable(): array
    {
        return [
            'toggleable' => (new Components\Component())
                ->view('livewire-powergrid::components.themes.bootstrap5.toggleable')
                ->toArray(),
        ];
        // Note: base, label, input, role removed in v7
    }

    public function filter(): array
    {
        return [
            'filter' => [
                'label' => 'form-label',                 // NEW in v7
                'boolean' => [
                    'view' => 'livewire-powergrid::components.themes.bootstrap5.filters.boolean',
                    'base' => 'form-select',
                    'select' => '',
                ],
                'number' => [
                    'view' => 'livewire-powergrid::components.themes.bootstrap5.filters.number',
                    'base' => '',                        // NEW - was missing in v6
                    'input' => 'form-control',
                ],
                'select' => [
                    'view' => 'livewire-powergrid::components.themes.bootstrap5.filters.select',
                    'base' => 'form-select',
                    'select' => '',
                ],
                'input_text' => [
                    'view' => 'livewire-powergrid::components.themes.bootstrap5.filters.input-text',
                    'base' => '',
                    'select' => '',
                    'input' => 'form-control',
                ],
                'input' => 'form-control',               // NEW - global input
            ],
        ];
    }
}
```

### Key Observations in the Migration:

1. **Fluent Builder Pattern**: Method chaining with closures replaces array returns
2. **Checkbox/Radio Nesting**: Now defined inside `->table()->checkbox()` and `->table()->radio()`
3. **SearchBox Nesting**: Now defined inside `->header()->searchBox()`
4. **Actions Moved**: `table.layout.actions` moved to `header.layout.actions`
5. **Footer Layout**: Uses nested `->footer()->layout()` sub-builder
6. **New Sections**: `layout()`, `header()`, `body()` are completely new
7. **Separate Methods**: `editable()`, `toggleable()`, `filter()` remain outside `struct()`
8. **Removed Keys**: Old keys like `table.layout.base`, `table.layout.div` are gone

### Step 4: Update Blade Views Styling Helpers
In your custom Blade files, replace the legacy `theme_style` helper with the new dot-notation `theme` helper:
```html
<!-- Legacy 6.x -->
<td class="{{ theme_style($theme, 'table.body.td') }}">...</td>

<!-- New 7.x -->
<td class="{{ theme('table.layout.td') }}">...</td>
```

### Step 5: Per-Component Theme Override

The method for overriding the theme on a per-component basis has been renamed:

```php
// v6 — removed
public function customThemeClass(): ?string
{
    return \App\PowerGridThemes\MyTheme::class;
}

// v7 — returns a Theme instance
public function template(): ?Theme
{
    return new \App\PowerGridThemes\MyTheme();
}
```

---

## 5. Configuration File Changes

The `config/livewire-powergrid.php` file has new and removed keys.

### Removed Keys

Remove these entries if present in your published config:

```php
// Remove — icon rendering is now inline via Blade
'icon_resources' => [...],

// Remove — Pulse integration removed
'record_enabled' => env('POWERGRID_RECORD_ENABLED', false),
```

Also remove `POWERGRID_RECORD_ENABLED` from your `.env` file.

### New Keys

These keys are available in v7:

```php
// State persistence driver for both PowerGrid and PowerGrid Lite
// Options: 'cookies' (default), 'session', 'cache'
'persist_driver' => 'cookies',

// Cache store name when using 'cache' as persist_driver (empty = default store)
'persist_driver_store' => '',

// Upper bound for rows fetched per page. The per-page value travels in the
// component state, so this ceiling keeps one request from loading an unbounded
// number of rows. Set to 0 to disable.
'max_per_page' => 1000,

// Filter placement now also supports panel modes that hold edits in a draft and
// only commit when the user presses "Apply filters" (no live/debounce requests):
// 'inline'   - filters inside the table (live, debounced)
// 'dropdown' - popover anchored to a Filter button (draft + Apply)
// 'flyout'   - drawer sliding from a side edge (draft + Apply)
'filter' => 'inline',

// Options for 'flyout'
'filter_flyout' => [
    'position'               => 'right', // 'left' or 'right'
    'close_on_escape'        => true,
    'close_on_click_outside' => true,
],
```

### Changed Keys

These keys changed shape or meaning between v6 and v7:

```php
// Export classes moved to the Plugins namespace, with an OpenSpout v5 driver.
// Update FQCNs referenced by your published config:
use PowerComponents\LivewirePowerGrid\Plugins\Export\OpenSpout\v5\{ExportToCsv, ExportToXLS};

'exportable' => [
    'default'      => 'openspout_v5',
    'openspout_v5' => [
        'xlsx' => ExportToXLS::class,
        'csv'  => ExportToCsv::class,
    ],
],

// Select plugin: the tom/slim option arrays were replaced by CDN asset URLs.
// The libraries are optional; when absent the filter degrades gracefully
// (see Section 2). Prefer bundling via npm and exposing the global.
'select' => [
    'default' => 'slim',
    'slim'    => [
        'cdn' => 'https://unpkg.com/slim-select@2.9.1/dist/slimselect.min.js',
        'css' => 'https://unpkg.com/slim-select@2.9.1/dist/slimselect.css',
    ],
    'tom'     => [
        'cdn' => 'https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js',
        'css' => 'https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css',
    ],
],
```

Also note: `plugins.flatpickr.locales` is keyed by your **app locale**
(e.g. `'pt_BR' => ['locale' => 'pt', 'dateFormat' => 'd/m/Y H:i', ...]`) — make sure
a matching key exists for every locale your application serves.

---

## 6. Blade Views & Layout Strategy

### View Namespace Change

PowerGrid 7.x changes the view namespace:

**v6:** `components.frameworks.[theme]`
**v7:** `components.themes.[theme]`

If you have custom view overrides, update the paths:

```bash
# Old path
resources/views/vendor/livewire-powergrid/components/frameworks/tailwind/

# New path
resources/views/vendor/livewire-powergrid/components/themes/tailwind/
```

### Updating Custom Views

If you maintain custom copies of PowerGrid blade views in `resources/views/vendor/livewire-powergrid`, you must update them:

1.  **Remove Theme Conditionals:** Remove any `@if($theme === 'bootstrap')` or framework-specific conditions. Under 7.x, the HTML structure is strictly separated from presentation logic.

2.  **Replace `theme_style()` with `theme()`:** Update all theme helper calls (see Section 4).

3.  **Remove `$theme` array usage:** The `$theme` variable is no longer passed to views.

4.  **Adapt to Micro-files:** The layout has been broken down into single-purpose components (e.g. `tr.blade.php`, `row.blade.php`, `tbody.blade.php`).

### Copying Latest Views

To get the latest view structure, manually copy views from the package:

```bash
cp -r vendor/power-components/livewire-powergrid/resources/views/components \
     resources/views/vendor/livewire-powergrid/
```

**Note:** PowerGrid 7.x does not include a `--views` flag in the publish command. Views must be copied manually.

---

## 7. Lazy Loading ("Load More") Migration

PowerGrid 7.x removes the chunked "load more" lazy loading system (`PowerGrid::lazy()`, `LazyManager`). This feature is **not related** to Livewire's `#[Lazy]` attribute (component deferred mounting) — do not use it as a replacement.

### Removed APIs

```php
// ❌ v6 - All removed in v7
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

PowerGrid::lazy()
    ->rowsPerChildren(10)
    ->items(1);

// LazyManager trait removed
use PowerComponents\LivewirePowerGrid\Concerns\LazyManager; // ❌ Removed

// Methods removed from PowerGridComponent
$this->loadMore();          // ❌ Removed
$this->hasLazyEnabled();    // ❌ Removed
$this->getLazyKeys();       // ❌ Removed
$this->canLoadMore();       // ❌ Removed
```

### Migration

There is **no 1:1 replacement** for the removed "load more" feature. Use PowerGrid's standard pagination instead — set a comfortable `$perPage` and let users navigate through the pages:

```php
public array $perPageValues = [10, 25, 50, 100];

public function setPerPage(int $perPage): void
{
    $this->perPage = $perPage;
}
```

If you need chunked/streaming processing (e.g. for exports over large datasets), PowerGrid uses `LazyCollection` under the hood for that use case — unrelated to table rendering.

---

## 8. Server-Side Action Buttons

Actions are no longer processed on the client side using JavaScript or saved in browser caches.

### Breaking Changes

**Removed JavaScript APIs:**
```javascript
// ❌ v6 - These no longer exist
window.pgActions
window.pgActionsHeader
```

### Migration Path

*   **Full Blade Rendering:** If your actions relied on JavaScript callbacks triggered from browser window properties (like `pgActions` or `pgActionsHeader`), you must refactor these to standard Livewire events or server-side actions.
*   **Button and Rule Customization:** Actions are rendered server-side. You can customize the button tags and apply rules directly using `setAttribute`, `slot`, `hide`, or custom `bladeComponent` parameters in the server-side action rule closures.

**Example:**
```php
Button::add('edit')
    ->slot('Edit')
    ->class('btn btn-primary')
    ->dispatch('edit', ['id' => 'id']);
```

---

## 9. Modular Plugins System

Features like inline editing (`editOnClick`), toggle inputs (`toggleable`), and the flatpickr date picker filter are now isolated in **Plugins**:
*   **Editable:** Managed by `EditablePlugin::class`.
*   **Toggleable:** Managed by `ToggleablePlugin::class`.
*   **DatePicker/Flatpickr:** Managed by `FlatpickrPlugin::class`.

### Global Plugin Registration
Plugins are loaded via the `PowerGrid` facade in your service provider's boot method:
```php
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Plugins\Editable\EditablePlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Toggleable\ToggleablePlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Flatpickr\FlatpickrPlugin;

PowerGrid::plugins([
    EditablePlugin::class,
    ToggleablePlugin::class,
    FlatpickrPlugin::class,
]);
```

### View Namespaces
Views for plugins are loaded via the `powergrid-plugins` namespace. If overriding plugin views, place your files in:
`resources/views/vendor/powergrid-plugins/[PluginName]/index.blade.php`

---

## 10. PowerGrid Lite

PowerGrid 7.x ships with a set of lightweight Blade components for building simple, themed tables without the full PowerGrid engine. No installation or configuration is required — components are registered automatically.

### Available Components

| Component | Props | Description |
|-----------|-------|-------------|
| `<x-pg-table>` | `:paginate`, `record-count` | Table wrapper. Renders the table skeleton and pagination via the active theme. |
| `<x-pg-columns>` | `sticky` | `<thead>` wrapper with optional sticky positioning |
| `<x-pg-column>` | `sortable`, `:sorted`, `:direction`, `field`, `align`, `sticky`, `checkbox` | `<th>` with optional sort icons or checkbox |
| `<x-pg-rows>` | — | `<tbody>` wrapper |
| `<x-pg-row>` | `:checkbox-value` | `<tr>` with optional checkbox column |
| `<x-pg-cell>` | `align`, `sticky` | `<td>` with alignment and sticky support |

> **Search and per-page are the user's responsibility.** Build them in your own Blade view and bind to `$search` and `$perPage` via `wire:model`. `<x-pg-table>` renders only the table structure and pagination.

### Available Traits

Add to your Livewire component as needed:

| Trait | Namespace | Public Properties |
|-------|-----------|-------------------|
| `WithSorting` | `Lite\Traits` | `$sortField`, `$sortDirection`, `$multiSort`, `$sortArray` |
| `WithSearch` | `Lite\Traits` | `$search` |
| `WithCheckbox` | `Lite\Traits` | `$checkboxValues`, `$checkboxAll` |
| `WithPersist` | `Lite\Traits` | — (config-driven, uses `$persist` array) |

### Example

```php
use Livewire\Component;
use PowerComponents\LivewirePowerGrid\Lite\Traits\{WithSorting, WithSearch, WithCheckbox};

class UsersTable extends Component
{
    use WithSorting, WithSearch, WithCheckbox;

    public function render()
    {
        return view('livewire.users-table', [
            'users' => \App\Models\User::query()
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orderBy($this->sortField ?: 'name', $this->sortDirection)
                ->paginate(15),
        ]);
    }
}
```

```blade
{{-- Search and per-page are built by you, bound to $search and $perPage --}}
<div class="flex items-center justify-between gap-3 mb-3">
    <input wire:model.live.debounce.700ms="search" type="text" placeholder="Search..." />
    <select wire:model.live="perPage">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
    </select>
</div>

{{-- Table renders structure + pagination via the active theme --}}
<x-pg-table :paginate="$users" record-count="full">
    <x-pg-columns>
        <x-pg-column checkbox />
        <x-pg-column sortable field="name"
            :sorted="$this->isSorted('name')"
            :direction="$this->sortDirectionFor('name')"
            wire:click="sortBy('name')">
            Name
        </x-pg-column>
        <x-pg-column align="end">Email</x-pg-column>
    </x-pg-columns>

    <x-pg-rows>
        @foreach ($users as $user)
            <x-pg-row :checkbox-value="$user->id">
                <x-pg-cell>{{ $user->name }}</x-pg-cell>
                <x-pg-cell align="end">{{ $user->email }}</x-pg-cell>
            </x-pg-row>
        @endforeach
    </x-pg-rows>
</x-pg-table>
```

### State Persistence with `WithPersist`

Add `WithPersist` and declare a `$persist` array with the items to store:

```php
use WithSorting, WithPersist;

public array $persist = ['sorting', 'perPage'];
```

Supported items: `sorting`, `checkbox`, `perPage`. The driver is configured via `persist_driver` in `config/livewire-powergrid.php`.

> **No migration needed.** PowerGrid Lite is an additive feature — existing components are unaffected.

---

## 11. Testing Strategy

If you write feature tests for your PowerGrid components:
*   **Do Not Use Shared Fixtures:** Do not rely on shared component classes (like a global `DishesTable`) across your test suite.
*   **Runtime Mini-Components:** Define your test tables at runtime using anonymous classes inside the test file to ensure they are self-sufficient and immune to style/theme dependencies:
    ```php
    $component = new class() extends PowerGridComponent {
        public string $tableName = 'test-table';
        public function datasource() {
            return collect([['id' => 1, 'name' => 'Item']]);
        }
        public function columns(): array {
            return [Column::make('Name', 'name')];
        }
    };
    ```

### Testing panel filters (`dropdown` / `flyout`)

Panel modes never filter live: edits land in the **draft** state and only commit on
Apply. In tests, drive them through `draftFilters.*` + `applyFilters()` — including
date ranges, whose raw value is the flatpickr `formatted` string:

```php
Livewire::test(SimonHistoryTable::class)
    ->call('fetchDatasource')
    ->set('draftFilters.datetime.created_at_formatted.formatted', '2026-08-04 00:00 to 2026-08-06 23:59')
    ->call('applyFilters')
    ->assertSee('INRANGE_CONVERSATION')
    ->assertDontSee('OUTRANGE_CONVERSATION');
```

Note the datetime filter is keyed by the **field name** (`created_at_formatted`),
not by the database column.

---

## 12. Pre-Migration Checklist

Before starting the upgrade, audit your codebase for affected areas:

### Search for Breaking Changes

```bash
# 1. Helper functions
grep -r "theme_style" .
grep -r "isBootstrap5" .
grep -r "isTailwind" .

# 2. Lazy loading
grep -r "PowerGrid::lazy()" .
grep -r "LazyManager" .
grep -r "loadMore()" .

# 3. View overrides
find resources/views/vendor/livewire-powergrid/components/frameworks -type f

# 4. Theme array usage in Blade
grep -r '\$theme\[' resources/views/

# 5. JavaScript actions
grep -r "window.pgActions" .
grep -r "window.pgActionsHeader" .

# 6. Configuration file
cat config/livewire-powergrid.php

# 7. Custom theme classes
grep -r "extends Theme" app/
grep -r "public function table()" app/
grep -r "public function apply()" app/

# 8. Legacy dist assets (see Section 18)
grep -rn "livewire-powergrid/dist" resources/ vite.config.js

# 9. Old export class references (see Section 5)
grep -rn "Components\\\\Exports\\\\OpenSpout" config/ app/
```

### Components to Review

- [ ] Custom theme classes
- [ ] Custom Blade view overrides
- [ ] Components using lazy loading
- [ ] Components with custom actions
- [ ] JavaScript code interacting with PowerGrid
- [ ] Tests using PowerGrid components
- [ ] Fields closures returning HTML strings (see Section 17)
- [ ] Asset pipeline: CSS/JS imports and Tailwind version (see Section 18)

---

## 13. Migration Effort Estimates

Plan your migration time based on project complexity:

| Project Size | Components | Custom Themes | Custom Views | Estimated Effort | Risk Level |
|--------------|------------|---------------|--------------|------------------|------------|
| **Small** | 1-3 | 0 | Few | 8-12 hours | Medium |
| **Small** | 1-3 | 1 | Few | 12-16 hours | Medium-High |
| **Medium** | 4-10 | 0-1 | Several | 3-5 days | High |
| **Medium** | 4-10 | 2+ | Many | 5-7 days | High |
| **Large** | 10-20 | 1-3 | Many | 2-3 weeks | Very High |
| **Enterprise** | 20+ | Multiple | Extensive | 4-8 weeks | Critical |

### Time Breakdown by Task

- **Configuration setup:** 30 minutes
- **Helper function replacement (per view):** 15-30 minutes
- **Custom theme migration (per theme):** 4-8 hours
- **Lazy loading migration (per component):** 2-4 hours
- **View path updates:** 1-2 hours
- **Action migration (per component):** 1-2 hours
- **Testing and validation:** 20-30% of total time

---

## 14. Step-by-Step Migration Guide

Follow these steps in order:

### Step 1: Update Dependencies (30 min)
```bash
# Update composer.json
composer require power-components/livewire-powergrid:^7.0
composer require power-components/partials

# Verify versions
composer show | grep power-components
```

### Step 2: Run Pre-Migration Audit (1-2 hours)
Run all search commands from Section 12 to identify affected areas

### Step 3: Migrate Custom Themes (4-8 hours each)
Follow the detailed guide in Section 4

### Step 4: Update Helper Functions (1-3 hours)
Replace `theme_style()` with `theme()` in all Blade views (see Section 3)

### Step 5: Update Configuration File (30 min)
Remove deprecated keys and add new ones (see Section 5)

### Step 6: Update View Paths (1-2 hours)
Move views from `frameworks/` to `themes/` namespace (see Section 6)

### Step 7: Migrate Lazy Loading (2-4 hours)
Migrate away from the removed "load more" lazy loading (see Section 7)

### Step 8: Update Actions (1-2 hours)
Remove JavaScript dependencies on `window.pgActions` (see Section 8)

### Step 9: Register Plugins (30 min)
Configure plugins in your service provider (see Section 9)

### Step 10: Wrap Raw HTML Fields in HtmlString (varies)
Audit `fields()` closures and wrap HTML-returning values in `HtmlString` (see Section 17)

### Step 11: Rebuild the Asset Pipeline (2-4 hours)
Import PowerGrid CSS/JS from package source, migrate to Tailwind 4 and add `@source`
scans (see Section 18)

### Step 12: Test Everything (varies)
- [ ] All tables render correctly
- [ ] Filters work
- [ ] Sorting works
- [ ] Pagination works
- [ ] Actions execute
- [ ] Exports work
- [ ] Custom themes applied
- [ ] Lazy loading works
- [ ] Editable/toggleable plugins work

### Step 13: Update Tests (2-4 hours)
Update feature tests to use runtime mini-components (see Section 11); drive panel
filters via `draftFilters` + `applyFilters()`

---

## 15. Troubleshooting

### Common Issues

#### "Class 'PowerComponents\LivewirePowerGrid\Themes\Bootstrap5' not found"
**Solution:** Bootstrap5 theme was removed. Update your config to use Tailwind, DaisyUI, or Flux.

#### "Call to undefined function theme_style()"
**Solution:** Replace with `theme()` helper (see Section 3).

#### "Call to undefined method lazy()"
**Solution:** The feature was removed with no direct replacement. Use standard pagination (see Section 7).

#### "View [livewire-powergrid::components.frameworks.tailwind.header] not found"
**Solution:** Update view namespace from `frameworks` to `themes` (see Section 6).

#### "window.pgActions is undefined"
**Solution:** Actions are now server-side rendered. Remove JavaScript dependencies (see Section 8).

#### HTML appearing escaped in cells (raw `<span ...>` shown as text)
**Solution:** v7 escapes plain-string field values with `e()`. Return an
`Illuminate\Support\HtmlString` from the `fields()` closure instead of a raw
string (see Section 17).

#### "ReferenceError: pgFlatpickr / pgExport / pgTomSelect is not defined" in console
**Solution:** Import the package entry in `resources/js/app.js` and rebuild Vite
(`resources/js/powergrid.js`, see Section 18). If you opted into
`livewire-powergrid.assets.auto_inject` instead, PowerGrid inlines only the
Alpine components the current table uses. Third-party libs (flatpickr,
tom-select, slim-select) still need to be installed and exposed on `window`.

#### PowerGrid styles missing or partially applied after upgrading
**Solution:** Import `resources/css/tailwind4.css` from the package and make sure
Tailwind 4 scans the package views plus your table/theme classes via `@source`
directives (see Section 18).

---

## 16. Engine Extraction to Turbine (Internal Architecture)

> **No action required for standard usage.** This section documents an internal
> architectural change for completeness. If you only use PowerGrid's public API
> (component, facades, `Column`, `Button`, filters, rules, setup objects), your
> code keeps working unchanged.

### What changed

The framework-agnostic data engine (search, filters, sort, pagination, export,
state persistence, summaries, action/rule resolution) was extracted into a
separate package, **`power-components/turbine`** (namespace
`PowerComponents\Turbine\`). PowerGrid 7.x now consumes Turbine as its engine.
This is why PowerGrid 7.x requires `illuminate/* 12.x|13.x` (see Section 1).

### Public API is unchanged

These keep the `PowerComponents\LivewirePowerGrid\` namespace and their existing
signatures — no import or code changes needed:

- `PowerGridComponent` (base class) and its `datasource()`, `fields()`, `columns()`,
  `filters()`, `setUp()`, etc. Only additive methods were introduced.
- Facades `PowerGrid`, `Filter`, `Rule` and helpers in `functions.php`.
- `Column`, `Button`, `PowerGridFields` and setup objects.

### Legacy imports still resolve

Classes physically moved into Turbine (`Column`, `Button`, filters, rules, setup
components) remain usable under their **old** `PowerComponents\LivewirePowerGrid\...`
FQCN, handled two ways:

- **Thin subclasses** for the commonly-constructed types: `Column`, `Button`,
  `PowerGridFields`, and `Components\SetUp\{Detail, Header, Footer, Exportable}`.
- **Lazy `class_alias` compat layer** (`Support\CompatAliases`, registered in the
  service provider) for the facade/factory-returned types:
  `Components\Filters\*`, `Components\Rules\*`, and `Components\SetUp\{Cache, Responsive, FilterBuilder}`.
  The alias makes the old FQCN identical to the Turbine class, so `use`,
  type-hints and `instanceof` all keep working — including against instances
  returned by `Filter::...()` / `Rule::...()`.

### Internal contract renames (only if you reached into internals)

If you hardcoded any of the following strings instead of using the provided
constants, update them:

- **Row-meta keys:** `__powergrid_rules` / `__powergrid_loop` / `__powergrid_actions`
  → `__turbine_rules` / `__turbine_loop` / `__turbine_actions`.
- **Rule-type strings (`RuleManager::TYPE_*`):** `pg:rows` / `pg:checkbox` /
  `pg:radio` / `pg:column` / `pg:editOnClick` / `pg:toggleable` → `turbine-*`.
  Always reference `RuleManager::TYPE_*`, never the literal string. The `TYPE_TOGGLEABLE`
  and `TYPE_EDIT_ON_CLICK` constants were removed.

### Preserved for compatibility (no change)

- Request key is still `powergrid` (with a `turbine` fallback).
- Persist configuration is still read from `livewire-powergrid.persist_driver`
  and `livewire-powergrid.persist_driver_store`.
- Persisted-state key prefix is still `pg:` — existing persisted grid state stays valid.

---

*Keep these guidelines in mind when upgrading to PowerGrid 7.x. The architectural changes are substantial, but the result is a cleaner, more maintainable codebase with better performance and flexibility.*

---

## Configurable Header Buttons (icons, titles and theme classes)

Header buttons no longer hardcode their icon or label. Each element resolves them, field by field, as:

**user `setUp()` configuration → theme token → package default**

### Configuring from the component

```php
use PowerComponents\Turbine\Components\SetUp\HeaderElement;

public function setUp(): array
{
    return [
        PowerGrid::header()
            ->showToggleColumns(fn (HeaderElement $element) => $element->icon('columns')->title('Columns'))
            ->showSoftDeletes(config: fn (HeaderElement $element) => $element->icon('icons.trash', ['class' => 'size-5']))
            ->showSearchInput(fn (HeaderElement $element) => $element->title('Search documents...'))
            ->filtersToggle(fn (HeaderElement $element) => $element->icon('funnel')->showLabel())
            ->clearFiltersPill(fn (HeaderElement $element) => $element->hideLabel())
            ->searchClearIcon(fn (HeaderElement $element) => $element->icon('x-mark')),

        PowerGrid::filterBuilder()->title('Filter')->icon('funnel'),

        PowerGrid::exportable('report')->title('Export')->icon('icons.download'),
    ];
}
```

`HeaderElement` methods: `icon(string $icon, array $iconAttributes = [])`, `iconAttributes()`, `withoutIcon()`,
`title()` (literal text or a lang key), `showLabel()` / `hideLabel()`, `view()` (swap the element blade).

Icons resolve exactly like `Button::icon()`: `funnel` renders `<x-funnel />` from your application,
`icons.funnel` renders `<x-icons.funnel />`, and `livewire-powergrid::icons.filter` renders a packaged icon.
The title is always emitted as `title` / `aria-label`; it becomes visible text only when `showLabel()` is set.

### Theme tokens

```
header.toggle_columns.{view, button, wrapper, icon, icon_class, label, menu, menu_item}
header.soft_deletes.{view, button, wrapper, icon, icon_class, label, menu, menu_item}
header.filters.{view, button, wrapper, icon, icon_class, label}
header.filter_builder.{view, button, wrapper, icon, icon_class, label, badge}
header.export.{view, button, wrapper, icon, icon_class, label, menu, menu_item}
header.enabled_filters.{view, wrapper, pill, pill_clear_all, icon, icon_class, label}
header.search_box.{icon, icon_clear}   # added to the existing search_box tokens
```

Set them with the new `Components\HeaderButton` builder:

```php
->header(fn (Components\Header $header) => $header
    ->toggleColumns(fn (Components\HeaderButton $button) => $button
        ->button('my-button-classes')
        ->icon('icons.columns')
        ->iconClass('size-5')
        ->menu('my-dropdown-classes')
    )
)
```

The `view` token (or `HeaderElement::view()`) replaces the element blade entirely.

### Notes

* Themes that define none of the new tokens keep their current look: every blade falls back to
  `header.layout.actions` and the previous hardcoded classes.
* Apps that published the package views keep their own copies (hardcoded icons). Re-publish them to
  get the configurable version.
* `FilterBuilder` is no longer hard-gated to the Flux theme: it renders whenever a view exists for the
  active theme, or when the theme points `header.filter_builder.view` at its own blade.
* New lang keys: `buttons.toggle_columns`, `buttons.soft_deletes`, `buttons.export`.

---

## 17. Field Output: Raw HTML Requires HtmlString

Field closures no longer render plain strings as raw HTML. The cell renderer escapes
scalar values with `e()`; only `Htmlable` values are emitted untouched
(`match` on `$rawContent`: `Htmlable` → `toHtml()`, scalar/`Stringable` → `e(...)`).

If your v6 tables returned HTML snippets (badges, styled spans, rendered Blade) as
plain strings, those now show up **escaped as visible text** in v7. Wrap them in
`Illuminate\Support\HtmlString`:

```php
// ❌ v6 - rendered raw, but v7 escapes this
->add('status_badge', fn (Order $model): string => $this->statusBadge($model->status));

// ✅ v7 - Htmlable values bypass escaping
use Illuminate\Support\HtmlString;

->add('status_badge', fn (Order $model): HtmlString => new HtmlString(
    '<span class="badge ...">'.$model->status->label.'</span>'
));
```

Audit every `fields()` closure whose return type is not purely textual — a quick way
is grepping your table classes for `return '<` and `Blade::render(` inside helpers
called from `fields()`.

---

## 18. Front-end Assets & Tailwind CSS 4

PowerGrid 7.x no longer ships pre-built `dist/` bundles. You compile PowerGrid's
assets from package source inside your own Vite/Tailwind build. A real-world
upgrade (large production app) surfaced this checklist:

### JavaScript

The pre-built `dist/powergrid` bundle is gone. Import the package entry in
your Vite bundle — `vite build` minifies it into `public/build/assets/app-*.js`.

```js
// resources/js/app.js

// ❌ v6
import "../../vendor/power-components/livewire-powergrid/dist/powergrid";

// ✅ v7 — Tailwind / DaisyUI
import "../../vendor/power-components/livewire-powergrid/resources/js/powergrid.js";

// ✅ v7 — Flux (dropdowns/menus come from Flux JS, not pgDropdown/pgExport)
import "../../vendor/power-components/livewire-powergrid/resources/js/powergrid-flux.js";
```

```php
// config/livewire-powergrid.php
'assets' => [
    'auto_inject' => false, // true = PHP inlines files the table uses (no Vite)
    'minify' => true,       // only applies to auto_inject
],
```

Third-party libraries used by filters (flatpickr, tom-select, slim-select) are
still optional and must be installed and exposed on `window` when you use those
filters. Plugin JS (export, editable, toggleable, flatpickr, filter builder) is
injected only when that plugin is enabled on the table.

### CSS & Tailwind 4

Point your stylesheet at the package's Tailwind 4 entry (it imports `tailwindcss`,
the base layer and its own `@source` scans):

```css
/* resources/css/app.css */

/* ❌ v6 */
@import "./../../vendor/power-components/livewire-powergrid/dist/tailwind.css";
@tailwind base;
@tailwind components;
@tailwind utilities;

/* ✅ v7 */
@import "flatpickr/dist/flatpickr.css";          /* keep third-party deps you use */
@import "./../../vendor/power-components/livewire-powergrid/resources/css/tailwind4.css";

/* Tailwind 3 -> 4 bridging, if you keep a legacy JS config */
@config "../../tailwind.config.js";

/* Scan your own table/theme sources so their classes are generated */
@source '../../app/Livewire/**/*Table.php';
@source '../../app/Helpers/*Table.php';
@source '../../vendor/power-components/livewire-powergrid/resources/views/components/**/*.blade.php';
```

Build-pipeline changes required by Tailwind 4:

1. Add `@tailwindcss/vite` to `vite.config.js` plugins.
2. Delete `postcss.config.js` (Tailwind 4 handles processing itself).
3. Remove `presets` entries pointing at `vendor/**/tailwind.config.js`
   (e.g. WireUI or PowerGrid presets) from `tailwind.config.js`.
4. Keep explicit `@source` lines for paths Tailwind cannot auto-discover — anything
   under `vendor/` or referenced only from PHP classes will otherwise be pruned
   from the generated CSS.

> If other legacy libraries depended on utilities provided by removed presets,
> port those few rules into your own CSS (a small compatibility stylesheet) rather
> than restoring the preset.
