---
description: >
    Create or update a PowerGrid theme using ThemeManager, the Theme
    abstract class, and the struct() token map pattern
name: powergrid-theme
---

## What I do

- Create new custom theme classes extending `Theme` with a complete `struct()` implementation
- Update existing themes by adding or overriding tokens in `struct()`, `filter()`, `editable()`, or `toggleable()`
- Register custom views via `views()` for any theme-exclusive Blade components
- Wire per-component theme overrides via `customThemeClass()` or `template()` + `merge()`
- Validate that all required token keys are present and correctly namespaced
- Run any relevant tests after changes

## When to use me

Use this when:

- A new UI theme needs to be added (e.g. Bootstrap, Flowbite, ShadCN)
- An existing theme's tokens need to be changed or extended
- A specific PowerGrid component needs a one-off theme override
- Token keys are missing or misnamed and causing empty class output

## How to use me

### Example 1: Create a new theme class

```
Use the 'powergrid-theme' skill to create a new theme for Bootstrap 5.
Theme class: src/Themes/Bootstrap.php
```

### Example 2: Update tokens in an existing theme

```
Use the 'powergrid-theme' skill to update the DaisyUI theme:
  - Change table.layout.thead to use 'bg-neutral text-neutral-content'
  - Add filter.boolean.select override for DaisyUI input sizing
```

### Example 3: Per-component override

```
Use the 'powergrid-theme' skill to apply a partial theme override to
  MyTableComponent so that table.layout.tr has an extra 'stripe' class.
```

---

## Architecture Overview

### Key Files

| File | Role |
|---|---|
| `src/Themes/Theme.php` | Abstract base — all themes extend this |
| `src/Themes/Tailwind.php` | Default theme; fallback for missing tokens and views |
| `src/Themes/DaisyUI.php` | DaisyUI component library theme |
| `src/Themes/Flux.php` | Livewire Flux UI theme |
| `src/Themes/Components/ThemeBuilder.php` | Fluent builder used inside `struct()` |
| `src/Themes/Components/Header.php` | Header section builder |
| `src/Themes/Components/Table.php` | Table section builder |
| `src/Themes/Components/Footer.php` | Footer section builder |
| `src/Themes/Components/Layout.php` | CSS class grouping within a section |
| `src/Themes/Components/Filter.php` | Filter token builder used in `filter()` |
| `src/Themes/Components/Component.php` | Generic sub-component builder |
| `src/Support/ThemeManager.php` | Static accessor — reads from `app('powergrid.theme')` |
| `src/functions.php` | Global helpers `theme()` and `theme_view()` |
| `src/PowerGridComponent.php` | Boots theme into IoC on every Livewire request |

### Binding Flow

```
config('livewire-powergrid.theme')   →  e.g. Tailwind::class
         ↓
PowerGridComponent::boot()
  → customThemeClass() override?     (per-component class swap)
  → template() override?             (per-component token merge)
  → app()->instance('powergrid.theme', $themeInstance)
         ↓
ThemeManager::theme('table.layout.td')
  → app('powergrid.theme')->resolveTokens()
  → data_get($tokens, 'table.layout.td', $default)
         ↓
Blade: {{ theme('table.layout.td') }}
       {{ theme_view('pagination') }}
```

### Token Resolution Order

`resolveTokens()` builds tokens in this order:

1. Parent theme tokens (via `parentTheme` chain, default = `Tailwind`)
2. Current theme `struct()` (deep-merged via `array_replace_recursive`)
3. `filter()` return value (merged in)
4. `editable()` return value (merged in)
5. `toggleable()` return value (merged in)

Missing tokens always fall through to `Tailwind` automatically.

### View Resolution Order (`resolveView($alias)`)

1. Explicit `views()` map on the current theme
2. Token at `$alias` directly (e.g. `header.view`)
3. Token at `view_$alias` (e.g. `header.view_export`)
4. Token at `$alias.view` (e.g. `header.export.view`)
5. Special `search_box` alias handling
6. Section-level `view_$alias` scan across `header`, `table`, `footer`
7. **Automatic fallback**: `baseView + '.' + alias` — if `view()->exists()` returns true
8. Delegated to `parentTheme->resolveView($alias)` (ultimately Tailwind)

---

## Execution Checklist

Before I start, I will:

1. **Identify the goal** — new theme, token update, or per-component override?
2. **Read the target file** (if updating an existing theme)
3. **Check the complete struct shape** against the reference below
4. **Write or update the class**
5. **Register the class** in `config/livewire-powergrid.php` if it is a new default theme
6. **Run tests** after changes

---

## 5-Step Process

### 1. Identify Change Scope

Determine which of the three patterns applies:

| Pattern | When | Where |
|---|---|---|
| **New `Theme` subclass** | Adding a full UI framework | `src/Themes/MyTheme.php` |
| **Token override via `merge()`** | One-off tweak on a component | `template()` method in the component |
| **`customThemeClass()`** | Swap theme on a single component | Override in the component class |

---

### 2. Token Struct Reference

`struct()` uses a **fluent builder** (`ThemeBuilder`) and is organized into three top-level sections: `header`, `table`, `footer`.

CSS tokens live inside `->layout(Closure)`. Sub-components (checkbox, radio, searchBox, pagination) have their own closures. Feature views (export, toggleColumns, etc.) are resolved automatically via `baseView + alias` — **do not declare them in `struct()`**.

The three extra methods (`filter()`, `editable()`, `toggleable()`) are merged into `resolveTokens()` automatically — define them as separate methods, not inside `struct()`.

#### Complete struct() shape

```php
public function struct(): array
{
    return Components\ThemeBuilder::make($this->name())
        ->baseView('livewire-powergrid::components.frameworks.my-theme')
        ->header(fn (Components\Header $header) => $header
            ->view('header')                          // Blade view alias (prefixed with baseView if no '::')
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('')                       // outermost header wrapper
                ->subContainer('')                    // inner flex wrapper
                ->actionsContainer('')                // wraps action buttons
                ->actions('')                         // action button classes
            )
            ->searchBox(fn (Components\Component $searchBox) => $searchBox
                ->view('header.search')               // Blade view alias
                ->container('')                       // search box outer wrapper
                ->relativeMain('')                    // relative-positioned inner wrapper
                ->input('')                           // text input classes
                ->iconSearchWrapper('')               // wrapper around search icon
                ->iconCloseWrapper('')                // wrapper around close icon
                ->iconClose('')                       // close icon classes
                ->iconSearch('')                      // search icon classes
            )
        )
        ->table(fn (Components\Table $table) => $table
            ->view('table-base')                      // main table Blade view alias
            ->viewHeader('table.tr')                  // table header row view alias
            ->viewRow('table.row')                    // table body row view alias
            ->viewCols('table.cols')                  // table columns view alias
            ->viewThEmpty('table.th-empty')           // empty th view alias
            ->viewInlineFilters('table.inline-filters') // inline filters row view alias
            ->viewCheckboxAll('table.checkbox-all')   // "select all" checkbox view alias
            ->viewCheckboxRow('table.checkbox-row')   // per-row checkbox view alias
            ->viewRadioRow('table.radio-row')         // per-row radio view alias
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('')                       // outer table wrapper
                ->table('')                           // <table> element classes
                ->thead('')                           // <thead> classes
                ->tr('')                              // header <tr> classes
                ->th('')                              // <th> classes
                ->tbody('')                           // <tbody> classes
                ->td('')                              // <td> classes
            )
            ->checkbox(fn (Components\Component $checkbox) => $checkbox
                ->th('')                              // checkbox column <th> classes
                ->base('')                            // wrapper around the checkbox
                ->label('')                           // <label> classes
                ->input('')                           // <input type="checkbox"> classes
            )
            ->radio(fn (Components\Component $radio) => $radio
                ->th('')                              // radio column <th> classes
                ->base('')                            // wrapper around the radio
                ->label('')                           // <label> classes
                ->input('')                           // <input type="radio"> classes
            )
        )
        ->footer(fn (Components\Footer $footer) => $footer
            ->view('footer')                          // footer Blade view alias
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('')                       // footer wrapper classes
                ->select('')                          // per-page select classes
            )
            ->pagination(fn (Components\Component $pagination) => $pagination
                ->view('pagination')                  // pagination Blade view alias
            )
        )
        ->toArray();
}
```

> **View aliases**: if the value does NOT contain `::`, it is automatically prefixed with `baseView + '.'`. Use a fully-qualified `livewire-powergrid::...` path only when pointing to a **different** theme's view (e.g. Flux reusing Tailwind's footer).

#### filter() shape

```php
public function filter(): array
{
    return [
        'filter' => (new Components\Filter())
            ->label('')                               // filter label classes
            ->boolean(fn (Components\Component $c) => $c
                ->view('livewire-powergrid::components.frameworks.tailwind.filters.boolean')
                ->base('')
                ->select('')
            )
            ->datePicker(fn (Components\Component $c) => $c
                ->view('livewire-powergrid::components.frameworks.tailwind.filters.date-picker')
                ->base('')
                ->input('')
            )
            ->multiSelect(fn (Components\Component $c) => $c
                ->view('livewire-powergrid::components.frameworks.tailwind.filters.multi-select')
                ->base('')
                ->select('')
            )
            ->number(fn (Components\Component $c) => $c
                ->view('livewire-powergrid::components.frameworks.tailwind.filters.number')
                ->base('')
                ->input('')
            )
            ->select(fn (Components\Component $c) => $c
                ->view('livewire-powergrid::components.frameworks.tailwind.filters.select')
                ->base('')
                ->select('')
            )
            ->inputText(fn (Components\Component $c) => $c
                ->view('livewire-powergrid::components.frameworks.tailwind.filters.input-text')
                ->base('')
                ->select('')
                ->input('')
            )
            ->input('')                               // generic filter input classes
            ->toArray(),
    ];
}
```

#### editable() shape

```php
public function editable(): array
{
    return [
        'editable' => (new Components\Component())
            ->view('livewire-powergrid::components.frameworks.tailwind.editable')
            ->clickable('')
            ->input('')
            ->error('')
            ->toArray(),
    ];
}
```

#### toggleable() shape

```php
public function toggleable(): array
{
    return [
        'toggleable' => (new Components\Component())
            ->view('livewire-powergrid::components.frameworks.tailwind.toggleable')
            ->toArray(),
    ];
}
```

---

### 3. Theme Class Patterns

#### Full New Theme (inherits from Tailwind by default)

```php
<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class MyTheme extends Theme
{
    // parentTheme defaults to Tailwind::class — tokens not declared here
    // fall through to Tailwind automatically.

    public function struct(): array
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.frameworks.my-theme')
            ->header(fn (Components\Header $header) => $header
                ->view('header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('...')
                    // ...
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('...')
                    ->table('...')
                    // ...
                )
                ->checkbox(fn (Components\Component $checkbox) => $checkbox
                    ->input('...')
                )
                ->radio(fn (Components\Component $radio) => $radio
                    ->input('...')
                )
            )
            ->footer(fn (Components\Footer $footer) => $footer
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('...')
                    ->select('...')
                )
                ->pagination(fn (Components\Component $pagination) => $pagination
                    ->view('pagination')
                )
            )
            ->toArray();
    }

    public function filter(): array { /* ... */ }

    public function editable(): array { /* ... */ }

    public function toggleable(): array { /* ... */ }
}
```

#### Reusing Another Theme's Views

When your theme does not have its own Blade file, point `->view()` to a fully-qualified existing view:

```php
// Flux reuses Tailwind's header and footer Blade views:
->header(fn (Components\Header $header) => $header
    ->view('livewire-powergrid::components.frameworks.tailwind.header')
    // ...
)
->footer(fn (Components\Footer $footer) => $footer
    ->view('livewire-powergrid::components.frameworks.tailwind.footer')
    // ...
)
```

Do **not** declare `->view()` on sub-components (searchBox, pagination, etc.) if the sub-view does not exist in your theme — the fallback chain will resolve it via `parentTheme` automatically.

#### Partial Override via `merge()` (per-component)

```php
public function template(): ?Theme
{
    return Tailwind::make()->merge([
        'table' => [
            'layout' => [
                'tr' => 'stripe hover:bg-yellow-50',
            ],
        ],
    ]);
}
```

#### Class Swap via `customThemeClass()` (per-component)

```php
public function customThemeClass(): ?string
{
    return MyTheme::class;
}
```

---

### 4. Register a New Default Theme

After creating the class, update the config so it becomes the project default:

```php
// config/livewire-powergrid.php
'theme' => \PowerComponents\LivewirePowerGrid\Themes\MyTheme::class,
```

Or set it per-component without touching the config (see `customThemeClass()` above).

---

### 5. Validate and Test

After any change:

```bash
# Run theme-related tests
composer test -- --filter="ThemeTest|ThemeBuilderTest|PowerGridComponentThemeTest"

# Run the full suite to catch regressions
composer test
```

---

## Dot-Notation Token Access

Tokens are accessed via `data_get()` with dot notation in PHP and Blade:

```php
// PHP
ThemeManager::theme('table.layout.td');
ThemeManager::theme('filter.boolean.select', 'fallback-class');

// Global helper (Blade or PHP)
theme('header.layout.container');
theme('table.layout.table');
theme('table.checkbox.input');
theme('table.radio.input');
theme('filter.boolean.select');
theme_view('pagination');   // resolves a Blade view path by alias
theme_view('header.search');
```

Key structural changes from v6 → v7:

| v6 key | v7 key |
|---|---|
| `table.table` | `table.layout.table` |
| `table.container` | `table.layout.container` |
| `table.thead` | `table.layout.thead` |
| `table.tr` | `table.layout.tr` |
| `table.th` | `table.layout.th` |
| `table.td` | `table.layout.td` |
| `checkbox.input` | `table.checkbox.input` |
| `radio.input` | `table.radio.input` |
| `header.container` | `header.layout.container` |
| `header.sub_container` | `header.layout.sub_container` |
| `footer.container` | `footer.layout.container` |
| `footer.select` | `footer.layout.select` |
| `root` | `base_view` (managed by `ThemeBuilder::baseView()`) |

---

## Rules

- **`struct()` uses `ThemeBuilder`** — do not return a raw array from `struct()`, always use the fluent builder and call `->toArray()` at the end.
- **CSS tokens live inside `->layout(Closure)`** — direct string properties on `Header`, `Table`, and `Footer` are for view aliases only.
- **`filter()`, `editable()`, `toggleable()` are separate methods** — do not put them inside `struct()`. They are automatically merged into `resolveTokens()`.
- **Feature views are automatic** — export, toggleColumns, softDeletes, batchExport views are resolved by `baseView + alias` fallback. Do not declare them in `struct()`.
- **Sub-component views**: only declare `->view()` on a sub-component (searchBox, pagination) if your theme has a custom Blade file for it. Omit it to inherit from `parentTheme`.
- **`merge()` is deep** — it uses `array_replace_recursive()`, so you only need to provide the keys you want to change.
- **Do not bind `powergrid.theme` manually** — always let `PowerGridComponent::boot()` handle the IoC binding. Per-component overrides go through `customThemeClass()` or `template()`.

---

## Completion Checklist

Before closing the task, verify:

- [ ] `struct()` uses `ThemeBuilder` and calls `->toArray()`
- [ ] `baseView` set correctly via `->baseView('livewire-powergrid::...')`
- [ ] CSS tokens inside `->layout(Closure)`, not directly on section builders
- [ ] `filter()`, `editable()`, `toggleable()` defined as separate methods (if overriding)
- [ ] Sub-component `->view()` only declared when the theme has the Blade file
- [ ] Theme class registered in config (if new default)
- [ ] `ThemeTest`, `ThemeBuilderTest`, and `PowerGridComponentThemeTest` pass
- [ ] Full suite passes: `composer test`
- [ ] No raw `app()->instance('powergrid.theme', ...)` calls outside of boot flow
