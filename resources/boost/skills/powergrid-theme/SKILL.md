---
description: >
    Create or update a PowerGrid v7 theme using the Theme abstract class,
    per-section token methods (layout/header/table/footer/cols/tabs plus
    filter/editable/toggleable), config theme_overrides, or ArrayTheme
name: powergrid-theme
---

## What I do

- Restyle a token with **zero code** via `config('livewire-powergrid.theme_overrides')`
- Create a new theme class extending `Theme` with per-section token methods (`layout()`, `header()`, `table()`, `footer()`, `cols()`, `tabs()`, plus `filter()`, `editable()`, `toggleable()`)
- Author a data-first theme as a plain array with `ArrayTheme` (`fromArray()` / `fromFile()` or a subclass)
- Update an existing theme by adding or overriding tokens in the relevant section method
- Wire per-component overrides via `customThemeClass()` (swap the class) or `template()` + `merge()` (patch tokens)
- Register a theme by name (`PowerGridManager::registerTheme()`) and select it by name in config
- Run the theme test suite after changes

## When to use me

Use this when:

- A new UI theme needs to be added (e.g. Bootstrap, Flowbite, ShadCN)
- An existing theme's tokens need to be changed or extended
- A few token classes need to change with no theme class at all (config `theme_overrides`)
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

### Example 3: No-code override via config

```
Use the 'powergrid-theme' skill to make table headers bolder for every table
via config('livewire-powergrid.theme_overrides'), without a Theme class.
```

### Example 4: Per-component override

```
Use the 'powergrid-theme' skill to apply a partial theme override to
MyTableComponent so that table.layout.tr gets an extra 'stripe' class.
```

---

## How it fits together (the short version)

A theme is a PHP class in `src/Themes/`. It extends `Theme` (`src/Themes/Theme.php`) and returns a token map. Blade views read tokens with the `theme()` / `theme_view()` helpers, e.g. `{{ theme('table.layout.td') }}` and `{{ theme_view('pagination') }}`.

There are **three ways to change theming**, cheapest first:

1. **No-code overrides (`config('livewire-powergrid.theme_overrides')`).** A nested token array in `resources/config/livewire-powergrid.php`, merged **last** (highest precedence) in `resolveTokens()`. Restyle any token without touching a Theme class:
   ```php
   'theme_overrides' => [
       'table' => ['layout' => ['th' => 'font-bold px-4 py-3']],
   ],
   ```
2. **Section methods on a Theme class.** Each token group is its own **public method** returning that group's slice — `layout()`, `header()`, `table()`, `footer()`, `cols()`, `tabs()`, plus `filter()`, `editable()`, `toggleable()`. `Theme::themeTokenMethods()` lists them and `resolveTokens()` auto-merges each.
3. **`ArrayTheme`** — a data-first theme authored as a plain nested array (no builder). See below.

**`struct()` is now tiny.** It only sets the base view; it does **not** carry the whole token tree any more:

```php
public function struct(): Components\ThemeBuilder
{
    return Components\ThemeBuilder::make($this->name())->baseView($this->baseView());
}

protected function baseView(): string
{
    return 'livewire-powergrid::components.themes.tailwind';
}
```

Everything else lives in the section methods. A **section method** can be written two equivalent ways:

- **Plain nested array** (6.x-familiar):
  ```php
  public function footer(): array
  {
      return ['footer' => ['pagination' => ['item' => 'btn ...']]];
  }
  ```
- **Fluent + type-safe via the `section()` helper** on `Theme` (it returns `['footer' => [...]]` with `view*` tokens baseView-prefixed):
  ```php
  public function footer(): array
  {
      return $this->section('footer', fn (Components\Footer $f) => $f
          ->layout(fn (Components\Layout $l) => $l->container('...')->select('...'))
          ->pagination('pagination'));
  }
  ```

The three canonical themes are the patterns to copy:

- `src/Themes/Tailwind.php` — the base theme; full section methods; `parentTheme = null`
- `src/Themes/DaisyUI.php` — a token-only subclass (`parentTheme = Tailwind::class`) that ships **zero blades** — imitate this
- `src/Themes/Flux.php` — a subclass that keeps its own `<flux:*>` blades only where the HTML genuinely differs

Inheritance is explicit: `Theme::$parentTheme` defaults to `null`. Set `protected ?string $parentTheme = Tailwind::class;` and a child overrides **only the sections it changes** — every other token, view, filter, editable, or toggleable value falls through to Tailwind. (DaisyUI and Flux both do this.)

**Prefer tokens over new blades.** DaisyUI ships no Blade files at all: `resources/views/components/themes/daisyui/` no longer exists. When a token names a view the theme does not ship, `Theme::doResolveView()` inherits the parent's blade. Only add a Blade file when the markup is genuinely different (as Flux does for its `<flux:*>` components).

**Selecting a theme.** `config('livewire-powergrid.theme')` accepts a registered **name** (`'tailwind'`, `'daisyui'`, `'flux'`) or an FQCN. Names live in `PowerGridManager::$themes` (default `DEFAULT_THEMES`); register your own with `PowerGridManager::registerTheme('bootstrap', BootstrapTheme::class)` and `PowerGridManager::resolveThemeClass()` resolves it.

Full token maps, the builder API, and the resolution order are in **`REFERENCE.md`**.

---

## Workflow

1. **Identify the goal.** A few token classes (no-code), a new/updated theme class, an array theme, or a per-component override?
2. **Read the closest real theme** — `Tailwind.php` (the full, authoritative section methods and token surface), `DaisyUI.php` (a token-only subclass), `Flux.php` (a subclass with a few of its own blades) — and, if updating, the target file. **Do not hand-write a token list from memory; diff against `Tailwind.php`.**
3. **Make the change:**
   - *A few classes only* → add them to `config('livewire-powergrid.theme_overrides')` (no class needed).
   - *New theme* → create `src/Themes/MyTheme.php` from the template below (tiny `struct()` + section methods).
   - *Token change* → edit the relevant section method (`layout()`, `header()`, `table()`, `footer()`, `cols()`, `tabs()`, `filter()`, `editable()`, `toggleable()`).
   - *Data-first theme* → use `ArrayTheme` (`fromArray()` / `fromFile()` or a subclass).
   - *Per-component* → use `customThemeClass()` or `template()` + `merge()` (see below).
4. **Register it** (new default theme only) in `resources/config/livewire-powergrid.php`, by name or FQCN:
   ```php
   'theme' => 'my-theme', // registered via PowerGridManager::registerTheme('my-theme', MyTheme::class)
   // 'theme' => \PowerComponents\LivewirePowerGrid\Themes\MyTheme::class, // FQCN also works
   ```
5. **Test:**
   ```bash
   composer test -- --filter="ThemeTest|ThemeBuilderTest|PowerGridComponentThemeTest"
   composer test
   ```

---

## Minimal new-theme template

Copy this, rename the class, point `baseView()` at your own view folder, and fill the classes in the section methods. It inherits Tailwind's every undeclared token, view, `filter()`, `editable()`, and `toggleable()` — override a section only where your framework differs. Ship a Blade file only for markup that genuinely differs; otherwise omit `->view()` and inherit Tailwind's blade (DaisyUI ships none).

```php
<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class MyTheme extends Theme
{
    // Inherit Tailwind for everything not declared below.
    protected ?string $parentTheme = Tailwind::class;

    // struct() only carries the base view. Every token group is its own method.
    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())->baseView($this->baseView());
    }

    protected function baseView(): string
    {
        return 'livewire-powergrid::components.themes.my-theme';
    }

    /** @return array<string, mixed> */
    public function layout(): array
    {
        return $this->section('layout', fn (Components\Layout $layout) => $layout
            ->wrapper('space-y-4')
            ->card('rounded-xl border')
            ->outsideFilters('')
        );
    }

    /** @return array<string, mixed> */
    public function table(): array
    {
        return $this->section('table', fn (Components\Table $table) => $table
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('overflow-x-auto relative border-t')
                ->table('min-w-full')
                ->thead('bg-zinc-100')
                ->tr('border-b')
                ->th('px-3 py-3 text-left text-xs')
                ->thActions('px-3 py-3 text-end text-xs')
                ->tbody('')
                ->td('px-3 py-2')
                ->tdActions('px-3 py-2 text-end')
            )
            ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox
                ->input('h-4 w-4')
            )
            ->radio(fn (Components\Radio $radio) => $radio
                ->input('rounded-full')
            )
        );
    }

    /** @return array<string, mixed> */
    public function tabs(): array
    {
        return $this->section('tabs', fn (Components\Tabs $tabs) => $tabs
            ->list('inline-flex items-center gap-1 rounded-xl border p-1')
            ->tab('rounded-lg px-3 py-1.5 text-sm font-medium')
            ->tabActive('bg-zinc-100 text-zinc-900')
            ->tabInactive('text-zinc-500')
            ->badge('rounded-full px-2 py-0.5 text-xs font-semibold')
            ->badgeActive('bg-blue-100 text-blue-700')
            ->badgeInactive('bg-zinc-100 text-zinc-600')
        );
    }

    // header(), footer(), cols(), filter(), editable(), toggleable() are optional here —
    // declare only the sections that differ from Tailwind. Read Tailwind.php for the full surface.
}
```

You can equally return plain nested arrays from any of these methods instead of using `section()`, e.g. `public function tabs(): array { return ['tabs' => ['list' => '...', 'tab' => '...']]; }`.

---

## `ArrayTheme` (data-first)

`src/Themes/ArrayTheme.php` lets you author a theme as a plain nested token array — no builder. Everything not declared falls back to the parent theme (Tailwind by default).

```php
// Ad-hoc, from an array:
$theme = \PowerComponents\LivewirePowerGrid\Themes\ArrayTheme::fromArray(
    ['footer' => ['pagination' => ['item' => 'btn ...']]],
    parentTheme: \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class,
    name: 'my-theme',
);

// Or from a PHP file that `return`s the token array:
$theme = \PowerComponents\LivewirePowerGrid\Themes\ArrayTheme::fromFile(base_path('themes/my-theme.php'));

// Or as a subclass:
class MyArrayTheme extends ArrayTheme
{
    protected ?string $parentTheme = Tailwind::class;

    public function struct(): array
    {
        return ['footer' => ['pagination' => ['item' => 'btn ...']]];
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

- **`struct()` only sets `baseView`.** It returns `Components\ThemeBuilder::make($this->name())->baseView($this->baseView())` (or a plain array for `ArrayTheme`). It no longer carries the whole token tree — every token group lives in its own section method.
- **Token groups are public methods.** `layout()`, `header()`, `table()`, `footer()`, `cols()`, `tabs()`, `filter()`, `editable()`, `toggleable()` — the list in `Theme::themeTokenMethods()`. Each returns its slice (`['<group>' => [...]]`), built either with `$this->section('<group>', fn (...) => ...)` or as a plain nested array. `resolveTokens()` merges them automatically.
- **Declare `parentTheme` explicitly.** `Theme::$parentTheme` defaults to `null`; set it to `Tailwind::class` so undeclared sections/tokens inherit from the base theme. A child overrides only the sections it changes.
- **No-code overrides win.** `config('livewire-powergrid.theme_overrides')` is merged **last** in `resolveTokens()` (after `struct()`, the parent theme, the section methods, and plugin tokens), so it overrides everything.
- **Correct closure type-hints.** Inside `section()` the sub-builders are distinct classes: `->searchBox(fn (Components\SearchBox $s) => ...)`, `->checkbox(fn (Components\Checkbox $c) => ...)`, `->radio(fn (Components\Radio $r) => ...)`, `->body(fn (Components\Body $b) => $b->tr(fn (Components\Tr $tr) => ...))`, `->cols(fn (Components\Cols $c) => ...)`, `->tabs(fn (Components\Tabs $t) => ...)`. Hinting these as `Components\Component` throws a `TypeError`.
- **Pagination is a string.** `->pagination('pagination')` — `Footer::pagination()` accepts `Closure|array|string`; all three shipped themes use the string alias.
- **CSS classes live in `->layout(Closure)`** for `header`/`table`/`footer`; `tabs` sets its classes directly on `Components\Tabs`.
- **Prefer tokens over new blades.** DaisyUI ships zero blades and inherits Tailwind's markup through `parentTheme` + the view-resolution fallback in `Theme::doResolveView()`. Add a Blade file only for genuinely different HTML (as Flux does).
- **The filter drawer is styled, not rebuilt.** `filter.flyout.*` (set with `->flyout(fn (Components\Flyout $f) => ...)`) drives the drawer used when `config('livewire-powergrid.filter')` is `flyout`. Its blade is shared by every theme through `parentTheme`, so override the classes and leave `view` alone. `panel` must carry the positioning (`fixed inset-y-0`) and stay above `overlay`; `panel_left`/`panel_right` add only the edge anchoring. The `dropdown` variant (`config('...filter')` = `dropdown`) works the same way.
- **`tabs` is a theme-aware token group.** The `tabs()` section sets `list`/`tab`/`tabActive`/`tabInactive`/`badge`/`badgeActive`/`badgeInactive` and (optionally) `view`. The shared base blade `resources/views/components/themes/tailwind/tabs.blade.php` reads these tokens and renders tab icons via `IconRenderer`. Point `tabs.view` at your own blade only when the markup differs (Flux points it at `powergrid-plugins::Tabs.themes.flux`).
- **`toggleable()` fills color tokens, not a view.** It sets `colorOn`/`colorOff`/`colorOnDark`/`colorOffDark`/`knobOn`; the shipped Toggleable blade reads them via `theme('toggleable.color_on')` etc. Do not give it a `->view()`.
- **Feature views are auto-resolved.** export, toggle-columns, soft-deletes, etc. resolve via `baseView + alias` (then the `parentTheme` chain, then `components.structure.*`). Do not declare them.
- **View aliases**: a value without `::` is prefixed with `baseView + '.'`. Use a fully-qualified `livewire-powergrid::...` path only to point at another theme's view.

---

## Completion checklist

- [ ] `struct()` returns `ThemeBuilder::make(...)->baseView(...)` only (or an array for `ArrayTheme`)
- [ ] `protected ?string $parentTheme = Tailwind::class;` is declared
- [ ] `baseView()` points at a real folder under `resources/views/components/themes/`
- [ ] Only the sections that differ from Tailwind are overridden, each as its own method (`layout`/`header`/`table`/`footer`/`cols`/`tabs`/`filter`/`editable`/`toggleable`)
- [ ] Sub-builder closures use the correct classes (`SearchBox`/`Checkbox`/`Radio`/`Body`/`Tr`/`Cols`/`Tabs`), never `Components\Component`
- [ ] `->pagination('pagination')` (string form)
- [ ] A Blade file is added only where the HTML genuinely differs; otherwise the parent's view is inherited
- [ ] Theme registered/selected in config by name or FQCN (new default only)
- [ ] `composer test` passes

See **`REFERENCE.md`** for the full section-method token map, the `filter()`/`editable()`/`toggleable()`/`tabs()` shapes, the complete builder API, dot-notation token keys, config `theme_overrides`, the theme registry, and the token/view resolution order.
