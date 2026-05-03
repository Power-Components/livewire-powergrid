---
compatibility: opencode
description: >
    Create or update a PowerGrid theme using ThemeManager, the Theme
    abstract class, and the struct() token map pattern
metadata:
    audience: laravel-developers
    framework: laravel-12
    package: livewire-powergrid
    agent: developer
name: powergrid-theme
---

## What I do

- Create new custom theme classes extending `Theme` with a complete `struct()` implementation
- Update existing themes by adding or overriding tokens in `struct()`
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
  - Change table.header.thead to use 'bg-neutral text-neutral-content'
  - Add search_box.input override for DaisyUI input sizing
```

### Example 3: Per-component override

```
Use the 'powergrid-theme' skill to apply a partial theme override to
  MyTableComponent so that table.body.tr.wrapper has an extra 'stripe' class.
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
| `src/Support/ThemeManager.php` | Static accessor — reads from `app('powergrid.theme')` |
| `src/functions.php` | Global helpers `theme()` and `theme_view()` |
| `src/PowerGridComponent.php` | Boots theme into IoC on every Livewire request |
| `src/Concerns/Base.php` | `customThemeClass()` hook on individual components |

### Binding Flow

```
config('livewire-powergrid.theme')   →  e.g. Tailwind::class
         ↓
PowerGridComponent::boot()
  → customThemeClass() override?     (per-component class swap)
  → template() override?             (per-component token merge)
  → app()->instance('powergrid.theme', $themeInstance)
         ↓
ThemeManager::theme('table.body.tr.wrapper')
  → app('powergrid.theme')->resolveTokens()
  → data_get($tokens, 'table.body.tr.wrapper', $default)
         ↓
Blade: {{ theme('table.body.tr.wrapper') }}
       {{ theme_view('pagination') }}
```

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

Every `struct()` **must** return an array with these top-level keys.
Missing keys fall through to `Tailwind` defaults automatically, but
explicitly declaring them is preferred for IDE support.

```php
public function struct(): array
{
    return [
        'name'  => 'my-theme',      // identifier string
        'root'  => 'livewire-powergrid::components.frameworks.tailwind', // view root

        'header' => [
            'container'     => '',
            'sub_container' => '',
            'actions'       => '',

            'batch_exporting' => [
                'container'         => '',
                'progress_bar'      => '',
                'finished_container'=> '',
                'finished_button'   => '',
            ],

            'export' => [
                'container'   => '',
                'button'      => '',
                'menu'        => '',
                'menu_item'   => '',
                'menu_button' => '',
            ],

            'toggle_columns' => [
                'container' => '',
                'button'    => '',
                'menu'      => '',
                'menu_item' => '',
            ],

            'soft_deletes' => [
                'container' => '',
                'button'    => '',
                'menu'      => '',
                'menu_item' => '',
            ],

            'enabled_filters' => [
                'container'        => '',
                'clear_all_button' => '',
                'filter_button'    => '',
            ],
        ],

        'table' => [
            'base'   => '',
            'layout' => [
                'base'      => '',
                'div'       => '',
                'container' => '',
                'actions'   => '',
            ],

            'header' => [
                'thead'      => '',
                'tr'         => '',
                'th'         => '',
                'th_wrapper' => '',
                'th_action'  => '',
            ],

            'body' => [
                'wrapper'     => '',
                'empty_state' => '',
                'tr' => [
                    'wrapper'    => '',
                    'summarize'  => '',
                    'responsive' => '',
                    'filters'    => '',
                ],
                'td' => [
                    'wrapper'         => '',
                    'empty_state'     => '',
                    'summarize'       => ['wrapper' => ''],
                    'filters'         => '',
                    'actions_wrapper' => '',
                ],
            ],

            'footer' => [
                'tr' => '',
            ],
        ],

        'footer' => [
            'select'               => '',
            'footer'               => '',
            'footer_with_pagination' => '',
        ],

        'layout' => [
            'table'      => '',   // view alias
            'header'     => '',   // view alias
            'pagination' => '',   // view alias
            'footer'     => '',   // view alias
        ],

        'cols' => [
            'div' => '',
        ],

        'editable' => [
            'view'      => '',   // view alias
            'clickable' => '',
            'input'     => '',
            'error'     => '',
        ],

        'toggleable' => [
            'view' => '',   // view alias
        ],

        'checkbox' => [
            'th'    => '',
            'base'  => '',
            'label' => '',
            'input' => '',
        ],

        'radio' => [
            'th'    => '',
            'base'  => '',
            'label' => '',
            'input' => '',
        ],

        'filter' => [
            'boolean' => [
                'view'   => '',
                'base'   => '',
                'select' => '',
            ],
            'date_picker' => [
                'view'  => '',
                'base'  => '',
                'input' => '',
            ],
            'multi_select' => [
                'view'   => '',
                'base'   => '',
                'select' => '',
            ],
            'number' => [
                'view'  => '',
                'base'  => '',
                'input' => '',
            ],
            'select' => [
                'view'   => '',
                'base'   => '',
                'select' => '',
            ],
            'input_text' => [
                'view'   => '',
                'base'   => '',
                'select' => '',
                'input'  => '',
            ],
            'input' => '',
        ],

        'search_box' => [
            'container'     => '',
            'relative_main' => '',
            'input'         => '',
            'icon_close'    => '',
            'icon_search'   => '',
        ],
    ];
}
```

---

### 3. Theme Class Pattern

#### Full New Theme

```php
<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class MyTheme extends Theme
{
    // Override views that differ from Tailwind.
    // Any alias not listed here falls back to Tailwind automatically.
    public function views(): array
    {
        return [
            'pagination' => 'livewire-powergrid::components.frameworks.my-theme.pagination',
        ];
    }

    /** @lang CSS */
    public function struct(): array
    {
        return [
            'name' => 'my-theme',
            'root' => 'livewire-powergrid::components.frameworks.tailwind',

            'table' => [
                'base' => 'w-full text-sm',
                // ... all other keys
            ],

            // ... remaining top-level keys
        ];
    }
}
```

#### Partial Override via `merge()` (per-component)

```php
// Inside a PowerGrid component:

public function template(): ?Theme
{
    return Tailwind::make()->merge([
        'table' => [
            'body' => [
                'tr' => [
                    'wrapper' => 'stripe hover:bg-yellow-50',
                ],
            ],
        ],
    ]);
}
```

#### Class Swap via `customThemeClass()` (per-component)

```php
// Inside a PowerGrid component:

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
php vendor/bin/pest tests/Feature/Support/ThemeManagerTest.php
php vendor/bin/pest tests/Feature/PowerGridComponentThemeTest.php

# Run the full suite to catch regressions
php vendor/bin/pest
```

Regenerate IDE autocomplete metadata if new token keys were added:

```bash
php artisan powergrid:generate-theme-meta
```

---

## Dot-Notation Token Access

Tokens are accessed via `data_get()` with dot notation in PHP and Blade:

```php
// PHP
ThemeManager::theme('table.body.tr.wrapper');
ThemeManager::theme('filter.boolean.select', 'fallback-class');

// Global helper (Blade or PHP)
theme('header.export.button');
theme_view('pagination');   // resolves a Blade view path by alias
```

The full flat list of valid keys is generated by:

```bash
php artisan powergrid:generate-theme-meta
# writes: .phpstorm.meta.php
```

---

## Rules

- **Every `struct()` key must be a string.** Arrays are only used for grouping; leaf values must be strings (CSS class strings or view alias strings).
- **`views()` is for Blade view aliases only.** CSS tokens belong in `struct()`.
- **`merge()` is deep** — it uses `array_replace_recursive()`, so you only need to provide the keys you want to change.
- **Missing view aliases fall back to Tailwind automatically** — you do not need to redeclare every view if you only override one.
- **Do not bind `powergrid.theme` manually** — always let `PowerGridComponent::boot()` handle the IoC binding. Per-component overrides go through `customThemeClass()` or `template()`.

---

## Completion Checklist

Before closing the task, verify:

- [ ] `struct()` implemented and all required top-level keys present
- [ ] `views()` declared for any non-Tailwind Blade views
- [ ] Theme class registered in config (if new default)
- [ ] `ThemeManagerTest` and `PowerGridComponentThemeTest` pass
- [ ] `powergrid:generate-theme-meta` run if new token keys were added
- [ ] No raw `app()->instance('powergrid.theme', ...)` calls outside of boot flow
