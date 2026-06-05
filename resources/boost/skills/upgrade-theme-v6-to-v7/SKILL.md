---
description: >
    Migrates a v6 Theme class to the v7 unified struct() architecture using fluent builder pattern
name: upgrade-theme-classfrom-v6-to-v7
---

## What I do

- Read a legacy v6 theme class (like Bootstrap, DaisyUI) from GitHub or local disk.
- Create a new v7 theme class (e.g., BootstrapV7.php) implementing the unified `struct()` pattern with fluent builder.
- Map all legacy CSS configurations from multiple v6 methods (`table()`, `header()`, `checkbox()`, `filterBoolean()`, etc.) into the new v7 fluent builder structure.
- Validate that the generated v7 class matches the exact structure of the base Tailwind v7 theme.
- Replace legacy usages in the codebase to point to the new `[ThemeName]V7` class.

## When to use me

Use me when:
- Porting legacy PowerGrid v6 themes to the new v7 architecture.
- Upgrading third-party custom themes that still use legacy array methods instead of the unified fluent `struct()`.

## How to use me

```
Use the 'upgrade-theme-classfrom-v6-to-v7' skill to migrate the v6 Bootstrap theme located at https://raw.githubusercontent.com/Power-Components/livewire-powergrid/6.x/src/Themes/Bootstrap5.php
```

## V7 Architecture Overview

### Critical Architectural Changes

V7 introduces a **completely new architecture** that is fundamentally different from v6:

#### V6 Architecture (Legacy)
```php
class Bootstrap5 extends Theme
{
    public function table(): array {
        return [
            'layout' => ['table' => 'table-hover'],
            'header' => ['th' => 'fw-bold'],
        ];
    }

    public function checkbox(): array {
        return ['th' => 'text-center'];
    }

    public function searchBox(): array {
        return ['input' => 'form-control'];
    }
}
```

#### V7 Architecture (Current)
```php
class Bootstrap5 extends Theme
{
    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.themes.bootstrap5')
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->table('table-hover')
                    ->th('fw-bold')
                )
                ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox
                    ->th('text-center')
                )
            )
            ->header(fn (Components\Header $header) => $header
                ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                    ->input('form-control')
                )
            );
    }
}
```

### Key Differences

1. **Fluent Builder Pattern**: V7 uses method chaining with closures instead of returning arrays
2. **Nested Structure**:
   - `checkbox` and `radio` are now nested **under** `table`
   - `searchBox` is now nested **under** `header`
   - `footer` uses nested `layout` sub-builder
3. **Type-Hinted Closures**: Each section uses typed closure parameters for IDE support
4. **Separate Methods**: `editable()`, `toggleable()`, and `filter()` remain as separate methods (not in `struct()`)
5. **New Sections**: V7 adds `layout`, `header`, and expands `searchBox` structure

### Component Classes Reference

When building `struct()`, you'll use these typed builders:

- `Components\ThemeBuilder` - Main builder (entry point)
- `Components\Layout` - For layout configurations (reused in multiple places)
- `Components\Header` - For header structure (new in v7)
- `Components\SearchBox` - For search box structure (expanded in v7)
- `Components\Table` - For table structure
- `Components\Body` - For body structure
- `Components\Tr` - For table row structure
- `Components\Checkbox` - For checkbox structure (now under table)
- `Components\Radio` - For radio structure (now under table)
- `Components\Cols` - For column structure
- `Components\Footer` - For footer structure
- `Components\Component` - For generic components (editable, toggleable in separate methods)

## Workflow

When invoked, execute the following steps precisely:

### 1. Analyze the v7 Base Structure

- Read the current `src/Themes/Tailwind.php` (v7) in the project.
- Extract the complete structure from the `struct()` method. This is the **Source of Truth** for the required structure.
- Note all 68 required keys (see mapping table below).

### 2. Fetch the v6 Legacy Theme

- Access the provided v6 theme file via URL or local path.
- Analyze all methods: `table()`, `cols()`, `footer()`, `checkbox()`, `radio()`, `editable()`, `toggleable()`, `searchBox()`, and all `filter*()` methods.
- Extract CSS class values from each method's returned array.

### 3. Create the New Theme Class (v7)

- Create a new file in `src/Themes/` named `[ThemeName].php` (e.g., `Bootstrap5.php`).
- Ensure it extends `\PowerComponents\LivewirePowerGrid\Themes\Theme`.
- Import required classes:
  ```php
  use PowerComponents\LivewirePowerGrid\Themes\Components;
  ```
- Create the unified `public function struct(): Components\ThemeBuilder` method.
- Map the legacy CSS classes from the v6 methods into the corresponding v7 fluent builder structure using the mapping table below.
- Create separate methods for `editable()`, `toggleable()`, and `filter()` (these don't go in `struct()`).
- Ensure the `baseView()` method points to the correct view path.

### 4. V6 to V7 Complete Mapping Table

Use this comprehensive mapping to translate ALL v6 methods into v7 structure:

#### Layout (NEW IN V7)

| V6 Source | V7 Target | V7 Method Chain | Notes |
|:----------|:----------|:----------------|:------|
| N/A | `layout.wrapper` | `->layout()->wrapper()` | NEW - Set to empty string or adapt |
| N/A | `layout.outsideFilters` | `->layout()->outsideFilters()` | NEW - Set to empty string or adapt |

#### Header Structure (NEW IN V7)

| V6 Source | V7 Target | V7 Method Chain | Notes |
|:----------|:----------|:----------------|:------|
| N/A | `header.view` | `->header()->view()` | NEW - Usually 'header' |
| N/A | `header.layout.container` | `->header()->layout()->container()` | NEW - Set based on framework |
| N/A | `header.layout.subContainer` | `->header()->layout()->subContainer()` | NEW - Set based on framework |
| N/A | `header.layout.actionsContainer` | `->header()->layout()->actionsContainer()` | NEW - Set based on framework |
| `table()['layout']['actions']` | `header.layout.actions` | `->header()->layout()->actions()` | MOVED from table in v6 |

#### SearchBox (RESTRUCTURED - now under header)

| V6 Source | V7 Target | V7 Method Chain | Notes |
|:----------|:----------|:----------------|:------|
| N/A | `header.searchBox.view` | `->header()->searchBox()->view()` | NEW - Usually 'header.search' |
| N/A | `header.searchBox.container` | `->header()->searchBox()->container()` | NEW - Set based on framework |
| N/A | `header.searchBox.relativeMain` | `->header()->searchBox()->relativeMain()` | NEW - Set based on framework |
| `searchBox()['input']` | `header.searchBox.input` | `->header()->searchBox()->input()` | MOVED under header |
| N/A | `header.searchBox.iconSearchWrapper` | `->header()->searchBox()->iconSearchWrapper()` | NEW - Set based on framework |
| N/A | `header.searchBox.iconCloseWrapper` | `->header()->searchBox()->iconCloseWrapper()` | NEW - Set based on framework |
| `searchBox()['iconClose']` | `header.searchBox.iconClose` | `->header()->searchBox()->iconClose()` | MOVED under header |
| `searchBox()['iconSearch']` | `header.searchBox.iconSearch` | `->header()->searchBox()->iconSearch()` | MOVED under header |

#### Table Layout (RESTRUCTURED)

| V6 Source | V7 Target | V7 Method Chain | Notes |
|:----------|:----------|:----------------|:------|
| `table()['layout']['container']` | `table.layout.container` | `->table()->layout()->container()` | |
| `table()['layout']['table']` | `table.layout.table` | `->table()->layout()->table()` | |
| `table()['header']['thead']` | `table.layout.thead` | `->table()->layout()->thead()` | |
| `table()['header']['tr']` | `table.layout.tr` | `->table()->layout()->tr()` | |
| `table()['header']['th']` | `table.layout.th` | `->table()->layout()->th()` | |
| `table()['header']['thAction']` | `table.layout.thActions` | `->table()->layout()->thActions()` | Renamed |
| `table()['body']['tbody']` | `table.layout.tbody` | `->table()->layout()->tbody()` | |
| `table()['body']['td']` | `table.layout.td` | `->table()->layout()->td()` | |
| `table()['body']['tdActionsContainer']` | `table.layout.tdActions` | `->table()->layout()->tdActions()` | Renamed |

#### Table Body Responsive (NEW IN V7)

| V6 Source | V7 Target | V7 Method Chain | Notes |
|:----------|:----------|:----------------|:------|
| N/A | `table.body.tr.responsive` | `->table()->body()->tr()->responsive()` | NEW - Set based on framework |
| N/A | `table.body.tr.responsiveToggleIcon` | `->table()->body()->tr()->responsiveToggleIcon()` | NEW - Set based on framework |

#### Table Checkbox (NESTED under table in v7)

| V6 Source | V7 Target | V7 Method Chain | Notes |
|:----------|:----------|:----------------|:------|
| `checkbox()['th']` | `table.checkbox.th` | `->table()->checkbox()->th()` | Now under table |
| `checkbox()['base']` | `table.checkbox.base` | `->table()->checkbox()->base()` | Now under table |
| `checkbox()['label']` | `table.checkbox.label` | `->table()->checkbox()->label()` | Now under table |
| `checkbox()['input']` | `table.checkbox.input` | `->table()->checkbox()->input()` | Now under table |

#### Table Radio (NESTED under table in v7)

| V6 Source | V7 Target | V7 Method Chain | Notes |
|:----------|:----------|:----------------|:------|
| `radio()['th']` | `table.radio.th` | `->table()->radio()->th()` | Now under table |
| `radio()['base']` | `table.radio.base` | `->table()->radio()->base()` | Now under table |
| `radio()['label']` | `table.radio.label` | `->table()->radio()->label()` | Now under table |
| `radio()['input']` | `table.radio.input` | `->table()->radio()->input()` | Now under table |

#### Cols (UNCHANGED)

| V6 Source | V7 Target | V7 Method Chain | Notes |
|:----------|:----------|:----------------|:------|
| `cols()['div']` | `cols.div` | `->cols()->div()` | |

#### Footer (RESTRUCTURED with nested layout)

| V6 Source | V7 Target | V7 Method Chain | Notes |
|:----------|:----------|:----------------|:------|
| `footer()['view']` | `footer.view` | `->footer()->view()` | |
| `footer()['footer']` | `footer.layout.container` | `->footer()->layout()->container()` | Renamed & nested |
| `footer()['select']` | `footer.layout.select` | `->footer()->layout()->select()` | Nested under layout |
| `footer()['footer_with_pagination']` | `footer.pagination` | `->footer()->pagination()` | Renamed |

#### Editable (SEPARATE METHOD - not in struct())

| V6 Source | V7 Target | Method | Notes |
|:----------|:----------|:-------|:------|
| `editable()['view']` | `editable.view` | `editable()` | Separate method |
| N/A | `editable.clickable` | `editable()` | NEW in v7 |
| `editable()['input']` | `editable.input` | `editable()` | Separate method |
| N/A | `editable.error` | `editable()` | NEW in v7 |

#### Toggleable (SEPARATE METHOD - simplified in v7)

| V6 Source | V7 Target | Method | Notes |
|:----------|:----------|:-------|:------|
| `toggleable()['view']` | `toggleable.view` | `toggleable()` | Only view in v7 |
| `toggleable()['base']` | ❌ REMOVED | N/A | No longer configurable |
| `toggleable()['label']` | ❌ REMOVED | N/A | No longer configurable |
| `toggleable()['input']` | ❌ REMOVED | N/A | No longer configurable |
| `toggleable()['role']` | ❌ REMOVED | N/A | No longer configurable |

#### Filters (SEPARATE METHOD - not in struct())

| V6 Source | V7 Target | Method | Notes |
|:----------|:----------|:-------|:------|
| N/A | `filter.label` | `filter()` | NEW in v7 |
| `filterBoolean()['view']` | `filter.boolean.view` | `filter()` | |
| `filterBoolean()['base']` | `filter.boolean.base` | `filter()` | |
| `filterBoolean()['select']` | `filter.boolean.select` | `filter()` | |
| `filterDatePicker()['view']` | `filter.date_picker.view` | `filter()` | |
| `filterDatePicker()['base']` | `filter.date_picker.base` | `filter()` | |
| `filterDatePicker()['input']` | `filter.date_picker.input` | `filter()` | |
| `filterMultiSelect()['view']` | `filter.multi_select.view` | `filter()` | |
| `filterMultiSelect()['base']` | `filter.multi_select.base` | `filter()` | |
| `filterMultiSelect()['select']` | `filter.multi_select.select` | `filter()` | |
| `filterNumber()['view']` | `filter.number.view` | `filter()` | |
| N/A | `filter.number.base` | `filter()` | NEW - was missing in v6 |
| `filterNumber()['input']` | `filter.number.input` | `filter()` | |
| `filterSelect()['view']` | `filter.select.view` | `filter()` | |
| `filterSelect()['base']` | `filter.select.base` | `filter()` | |
| `filterSelect()['select']` | `filter.select.select` | `filter()` | |
| `filterInputText()['view']` | `filter.input_text.view` | `filter()` | |
| `filterInputText()['base']` | `filter.input_text.base` | `filter()` | |
| `filterInputText()['select']` | `filter.input_text.select` | `filter()` | |
| `filterInputText()['input']` | `filter.input_text.input` | `filter()` | |
| N/A | `filter.input` | `filter()` | NEW - global input styles |

#### V6 Keys Removed in V7

These v6 keys have no v7 equivalent and should be discarded:

| V6 Key | Reason |
|:-------|:-------|
| `table()['layout']['base']` | Removed - no longer used |
| `table()['layout']['div']` | Removed - no longer used |
| `table()['body']['tbodyEmpty']` | Removed - simplified |
| `table()['body']['tdEmpty']` | Removed - simplified |
| `table()['body']['tdSummarize']` | Removed - simplified |
| `table()['body']['trSummarize']` | Removed - simplified |
| `table()['body']['tdFilters']` | Removed - simplified |
| `table()['body']['trFilters']` | Removed - simplified |

### 5. Complete Migration Example

#### V6 Bootstrap5 Theme (Legacy)

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
                'div' => '',
                'base' => 'card',
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

#### V7 Bootstrap5 Theme (Migrated)

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
            ->layout(fn (Components\Layout $layout) => $layout
                ->wrapper('')           // NEW in v7 - add if needed
                ->outsideFilters('')    // NEW in v7 - add if needed
            )
            ->header(fn (Components\Header $header) => $header
                ->view('header')        // NEW in v7
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('d-flex justify-content-between')  // NEW
                    ->subContainer('')                              // NEW
                    ->actionsContainer('')                          // NEW
                    ->actions('')                                   // from v6 table.layout.actions
                )
                ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                    ->view('header.search')                         // NEW
                    ->container('')                                 // NEW
                    ->relativeMain('')                              // NEW
                    ->input('form-control')                         // from v6
                    ->iconSearchWrapper('')                         // NEW
                    ->iconCloseWrapper('')                          // NEW
                    ->iconClose('bi bi-x')                          // from v6
                    ->iconSearch('bi bi-search')                    // from v6
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('my-0')                             // from v6
                    ->table('table-hover table-striped')            // from v6
                    ->thead('')                                     // from v6
                    ->tr('')                                        // from v6
                    ->th('fw-bold text-secondary')                  // from v6
                    ->thActions('text-center')                      // from v6 (thAction)
                    ->tbody('')                                     // from v6
                    ->td('align-middle text-nowrap')                // from v6
                    ->tdActions('')                                 // from v6 (tdActionsContainer)
                )
                ->body(fn (Components\Body $body) => $body
                    ->tr(fn (Components\Tr $tr) => $tr
                        ->responsive('')                            // NEW in v7
                        ->responsiveToggleIcon('')                  // NEW in v7
                    )
                )
                ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox
                    ->th('fs-6 text-center')                        // from v6 - now nested
                    ->base('form-check')                            // from v6 - now nested
                    ->label('form-check-label')                     // from v6 - now nested
                    ->input('form-check-input')                     // from v6 - now nested
                )
                ->radio(fn (Components\Radio $radio) => $radio
                    ->th('fs-6 text-center')                        // adapt from checkbox
                    ->base('form-check')                            // adapt from checkbox
                    ->label('form-check-label')                     // adapt from checkbox
                    ->input('form-check-input')                     // adapt from checkbox
                )
            )
            ->cols(fn (Components\Cols $cols) => $cols
                ->div('')                                           // from v6
            )
            ->footer(fn (Components\Footer $footer) => $footer
                ->view('footer')                                    // from v6 (without $this->root())
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('d-flex justify-content-between')  // from v6 footer.footer
                    ->select('form-select')                         // from v6
                )
                ->pagination('pagination')                          // from v6 footer_with_pagination
            );
    }

    public function editable(): array
    {
        return [
            'editable' => (new Components\Component())
                ->view('livewire-powergrid::components.themes.bootstrap5.editable')
                ->clickable('cursor-pointer')                       // NEW in v7
                ->input('form-control')
                ->error('is-invalid')                               // NEW in v7
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
                'label' => 'form-label',                            // NEW in v7
                'boolean' => [
                    'view' => 'livewire-powergrid::components.themes.bootstrap5.filters.boolean',
                    'base' => 'form-select',
                    'select' => '',
                ],
                'number' => [
                    'view' => 'livewire-powergrid::components.themes.bootstrap5.filters.number',
                    'base' => '',                                   // NEW - was missing in v6
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
                'input' => 'form-control',                          // NEW - global input
            ],
        ];
    }
}
```

### 6. Validation (Key Syncing)

After migration, run validation:

```bash
# Compare the new theme structure with Tailwind (base)
php artisan tinker
```

```php
$tailwind = new \PowerComponents\LivewirePowerGrid\Themes\Tailwind();
$bootstrap = new \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5();

// Get all keys from both themes
$tailwindKeys = array_keys(data_get($tailwind->struct()->toArray(), '*'));
$bootstrapKeys = array_keys(data_get($bootstrap->struct()->toArray(), '*'));

// Check for missing keys
$missing = array_diff($tailwindKeys, $bootstrapKeys);

if (empty($missing)) {
    echo "✅ All keys present!\n";
} else {
    echo "❌ Missing keys:\n";
    print_r($missing);
}
```

Ensure **every single key** present in Tailwind v7 is present in the new v7 theme. Fill missing keys with empty strings `''` or appropriate default values.

### 7. Update Usages (Refactoring)

- Search the codebase (using `grep` or `glob`) for references to the old theme class name.
- Update these references to point to the newly created theme class.
- Update any test files that reference the theme.
- Update configuration files if needed.

### 8. Testing

Run the test suite to ensure the theme works correctly:

```bash
php artisan test --filter=Theme
```

### 9. Documentation

Update any documentation that references the theme structure or migration process.

## Key Checklist

Before marking the migration complete, verify:

- [ ] All 68 struct() keys are present (compare with Tailwind.php)
- [ ] Fluent builder pattern is correctly implemented with closures
- [ ] Checkbox/Radio are nested under `table`
- [ ] SearchBox is nested under `header`
- [ ] Footer uses nested `layout` sub-builder
- [ ] New v7 keys (layout.wrapper, header.*, etc.) are added with appropriate values
- [ ] Removed v6 keys (table.layout.base, etc.) are not included
- [ ] Separate methods exist for editable(), toggleable(), filter()
- [ ] baseView() points to correct view path
- [ ] All tests pass
- [ ] No references to old theme class remain in codebase

## Summary

The v6 to v7 migration is **not a simple key renaming**. It requires:

1. **Understanding the fluent builder pattern** with nested closures
2. **Restructuring the hierarchy** (checkbox under table, searchBox under header)
3. **Adding new v7 keys** that didn't exist in v6
4. **Removing deprecated v6 keys** that no longer exist
5. **Proper type-hinting** for IDE support and validation

Follow this guide precisely to ensure a successful migration.
