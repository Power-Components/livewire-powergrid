---
description: >
    Create or update a PowerGrid v7 theme using the Theme abstract class,
    the ThemeBuilder struct() token map, and the filter()/editable()/toggleable()
    token methods
name: powergrid-theme
---

## What I do

- Create a new theme class extending `Theme` with a `struct()` built from `ThemeBuilder`
- Update an existing theme by adding or overriding tokens in `struct()`, `filter()`, `editable()`, or `toggleable()`
- Wire per-component overrides via `customThemeClass()` (swap the class) or `template()` + `merge()` (patch tokens)
- Register a new default theme in the published config
- Run the theme test suite after changes

## When to use me

Use this when:

- A new UI theme needs to be added (e.g. Bootstrap, Flowbite, ShadCN)
- An existing theme's tokens need to be changed or extended
- A single PowerGrid component needs a one-off theme override
- Token keys are missing or misnamed and produce empty class output

This skill is only about **creating and updating v7 themes**. It is self-contained — everything you need is here and in `REFERENCE.md` (same folder).

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
  - Add a filter.boolean.select override for DaisyUI input sizing
```

### Example 3: Per-component override

```
Use the 'powergrid-theme' skill to apply a partial theme override to
MyTableComponent so that table.layout.tr gets an extra 'stripe' class.
```

---

## How it fits together (the short version)

A theme is a PHP class in `src/Themes/`. It extends `Theme` (`src/Themes/Theme.php`) and returns a token map. Blade views read tokens with the `theme()` / `theme_view()` helpers, e.g. `{{ theme('table.layout.td') }}` and `{{ theme_view('pagination') }}`.

The three canonical themes are the patterns to copy:

- `src/Themes/Tailwind.php` — the base theme; every other theme inherits from it
- `src/Themes/DaisyUI.php` — a compact subclass (start here for a new theme)
- `src/Themes/Flux.php` — a subclass that also overrides `resolveTokens()`

`struct()` returns a `Components\ThemeBuilder` (fluent). It is split into top-level sections — `layout`, `header`, `table`, `cols`, `footer` — and CSS classes live inside each section's `->layout(Closure)`. Filters, editable cells, and the toggleable switch are **separate methods** (`filter()`, `editable()`, `toggleable()`), merged into the token map automatically by `resolveTokens()`.

Inheritance is explicit: `Theme::$parentTheme` defaults to `null`. Set `protected ?string $parentTheme = Tailwind::class;` so any token, filter, editable, or toggleable value you do not declare is inherited from Tailwind. (DaisyUI and Flux both do exactly this.)

Full token maps, the builder API, and the resolution order are in **`REFERENCE.md`**.

---

## Workflow

1. **Identify the goal.** New theme, token change on an existing theme, or a per-component override?
2. **Read the closest real theme** (`Tailwind.php` for the full map, `DaisyUI.php` for a lean subclass) and, if updating, the target file.
3. **Make the change:**
   - *New theme* → create `src/Themes/MyTheme.php` from the template below.
   - *Token change* → edit the relevant `->layout(...)`, `filter()`, `editable()`, or `toggleable()` value.
   - *Per-component* → use `customThemeClass()` or `template()` + `merge()` (see below).
4. **Register it** (new default theme only) in `resources/config/livewire-powergrid.php`:
   ```php
   'theme' => \PowerComponents\LivewirePowerGrid\Themes\MyTheme::class,
   ```
5. **Test:**
   ```bash
   composer test -- --filter="ThemeTest|ThemeBuilderTest|PowerGridComponentThemeTest"
   composer test
   ```

---

## Minimal new-theme template

Copy this, rename the class, point `baseView` at your own view folder, and fill the classes. It inherits Tailwind's `filter()`, `editable()`, and `toggleable()` — override those methods only if your framework needs different filter markup or switch colors (see `REFERENCE.md`).

```php
<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class MyTheme extends Theme
{
    // Inherit Tailwind's base tokens, filter(), editable() and toggleable().
    // Anything not declared below falls through to Tailwind.
    protected ?string $parentTheme = Tailwind::class;

    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.themes.my-theme')
            ->layout(fn (Components\Layout $layout) => $layout
                ->wrapper('space-y-4')
                ->outsideFilters('')
            )
            ->header(fn (Components\Header $header) => $header
                ->view('header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('md:flex md:flex-row w-full justify-between items-center mb-3')
                    ->subContainer('md:flex md:flex-row w-full gap-1')
                    ->actionsContainer('flex flex-row items-center text-sm flex-wrap gap-2')
                    ->actions('btn btn-sm')
                )
                ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                    ->view('header.search')
                    ->container('w-full md:w-auto ml-auto')
                    ->relativeMain('relative w-full md:w-80')
                    ->input('w-full')
                    ->iconSearchWrapper('absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none')
                    ->iconSearch('h-4 w-4')
                    ->iconCloseWrapper('absolute inset-y-0 right-0 flex items-center pr-1')
                    ->iconClose('h-4 w-4')
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('overflow-x-auto rounded-t-lg relative border')
                    ->table('min-w-full')
                    ->thead('bg-zinc-100')
                    ->tr('border-b')
                    ->th('px-3 py-3 text-left text-xs')
                    ->thActions('px-3 py-3 text-end text-xs')
                    ->tbody('')
                    ->td('px-3 py-2')
                    ->tdActions('px-3 py-2 text-end')
                )
                ->body(fn (Components\Body $body) => $body
                    ->tr(fn (Components\Tr $tr) => $tr
                        ->responsive('text-sm break-words w-full')
                        ->responsiveToggleIcon('w-5 h-5 transition-all')
                    )
                )
                ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox
                    ->th('px-6 py-3')
                    ->input('h-4 w-4')
                )
                ->radio(fn (Components\Radio $radio) => $radio
                    ->th('px-6 py-3')
                    ->input('rounded-full')
                )
            )
            ->cols(fn (Components\Cols $cols) => $cols
                ->div('flex items-center gap-1')
            )
            ->footer(fn (Components\Footer $footer) => $footer
                ->view('footer')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('flex items-center px-3 py-2 border')
                    ->select('py-1.5 px-3 pr-8')
                )
                ->pagination('pagination')
            );
    }
}
```

---

## Per-component overrides

Both hooks live on your Livewire PowerGrid component (see `src/Concerns/Base.php` and `src/PowerGridComponent.php`), and are applied in `boot()` — never bind `powergrid.theme` yourself.

**Swap the whole theme class for one component:**

```php
public function customThemeClass(): ?string
{
    return MyTheme::class;
}
```

**Patch a few tokens for one component** (deep merge, so pass only the keys you change):

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

---

## Rules

- **`struct()` returns `Components\ThemeBuilder`.** Type the method `public function struct(): Components\ThemeBuilder` and return the builder directly. Do **not** call `->toArray()` on it — `resolveTokens()` handles that. (`filter()`, `editable()`, and `toggleable()` return plain arrays and *do* end each `Component` chain with `->toArray()`.)
- **Declare `parentTheme` explicitly.** `Theme::$parentTheme` defaults to `null`; set it to `Tailwind::class` so undeclared tokens/filters inherit from the base theme. Tokens do **not** fall through to Tailwind unless you opt in this way.
- **Correct closure type-hints.** The sub-builders are distinct classes: `->searchBox(fn (Components\SearchBox $s) => ...)`, `->checkbox(fn (Components\Checkbox $c) => ...)`, `->radio(fn (Components\Radio $r) => ...)`, `->body(fn (Components\Body $b) => $b->tr(fn (Components\Tr $tr) => ...))`, `->cols(fn (Components\Cols $c) => ...)`. Hinting these as `Components\Component` throws a `TypeError`.
- **Pagination is a string.** `->pagination('pagination')` — `Footer::pagination()` accepts `Closure|array|string`; all three shipped themes use the string alias.
- **CSS classes live in `->layout(Closure)`.** Direct string properties on `Header`/`Table`/`Footer` are for view aliases only.
- **`filter()`, `editable()`, `toggleable()` are separate methods**, not part of `struct()`. They are merged into `resolveTokens()` automatically.
- **`toggleable()` fills color tokens, not a view.** It sets `colorOn`/`colorOff`/`colorOnDark`/`colorOffDark`/`knobOn`; the shipped Toggleable blade reads them via `theme('toggleable.color_on')` etc. Do not give it a `->view()`.
- **Feature views are auto-resolved.** export, toggle-columns, soft-deletes, etc. resolve via `baseView + alias` (then the `parentTheme` chain, then `components.structure.*`). Do not declare them in `struct()`.
- **View aliases**: a value without `::` is prefixed with `baseView + '.'`. Use a fully-qualified `livewire-powergrid::...` path only to point at another theme's view.

---

## Completion checklist

- [ ] `struct()` is typed `: Components\ThemeBuilder` and returns the builder (no trailing `->toArray()`)
- [ ] `protected ?string $parentTheme = Tailwind::class;` is declared
- [ ] `baseView` points at a real folder under `resources/views/components/themes/`
- [ ] Sub-builder closures use the correct classes (`SearchBox`/`Checkbox`/`Radio`/`Body`/`Tr`/`Cols`), never `Components\Component`
- [ ] `->pagination('pagination')` (string form)
- [ ] CSS classes are inside `->layout(Closure)`
- [ ] `filter()`/`editable()`/`toggleable()` overridden only if needed, as separate methods
- [ ] Theme registered in config (new default only)
- [ ] `composer test` passes

See **`REFERENCE.md`** for the full `struct()` token map, the `filter()`/`editable()`/`toggleable()` shapes, the complete builder API, dot-notation token keys, and the token/view resolution order.
