---
description: >
    Migrates a v6 Theme class to the v7 architecture: a tiny struct() plus
    per-section token methods (layout/header/table/footer/cols/tabs +
    filter/editable/toggleable) with parentTheme inheritance
name: upgrade-theme-v6-to-v7
---

## What I do

- Read a legacy v6 theme class (Bootstrap, DaisyUI, a custom third-party theme) from GitHub or local disk.
- Create a v7 theme class with a tiny `struct()` (base view only) plus the per-section token methods (`layout()`, `header()`, `table()`, `footer()`, `cols()`, `tabs()`, `filter()`, `editable()`, `toggleable()`).
- Map every legacy v6 array method (`table()`, `header()`, `checkbox()`, `searchBox()`, `filter*()`, ...) onto the correct v7 section and token key.
- Set `parentTheme = Tailwind::class` so the migrated theme only declares what actually differs.

This skill is the **canonical owner** of the complete v6 -> v7 mapping table, which lives in [`REFERENCE.md`](REFERENCE.md).

## When to use me

- Porting a legacy PowerGrid v6 theme to the v7 architecture.
- Upgrading a custom theme that still returns arrays from `table()`, `checkbox()`, `footer()`, etc. with the old (flat/renamed) keys.

## How to use me

```
Use the 'upgrade-theme-v6-to-v7' skill to migrate the v6 Bootstrap theme at
https://raw.githubusercontent.com/Power-Components/livewire-powergrid/6.x/src/Themes/Bootstrap5.php
```

## The good news: v7 is closer to v6 than you think

v7 did **not** collapse everything into one builder. Like v6, a theme is still a
set of **per-section methods that return arrays**. The migration is mostly
**renaming and re-nesting keys** and turning on inheritance. Four things change:

1. **`struct()` is tiny.** It only sets the base view — it does **not** carry the token tree:
   ```php
   public function struct(): Components\ThemeBuilder
   {
       return Components\ThemeBuilder::make($this->name())->baseView($this->baseView());
   }
   protected function baseView(): string { return 'livewire-powergrid::components.themes.bootstrap5'; }
   ```
2. **Token groups are per-section methods** returning `['<group>' => [...]]`. The group list is in `Theme::themeTokenMethods()`: `layout`, `header`, `table`, `footer`, `cols`, `tabs`, `filter`, `editable`, `toggleable`. Write each as a **plain nested array** (closest to v6) or with the fluent `$this->section('<group>', fn (...) => ...)` helper.
3. **The hierarchy is re-nested and renamed:**
   - `checkbox` and `radio` move **under** `table` (`table.checkbox.*`, `table.radio.*`).
   - `searchBox` moves **under** `header` (`header.search_box.*`).
   - `footer` gains a nested `layout`; the old `footer_with_pagination` becomes `pagination`.
   - `table.header.*` / `table.body.*` flatten to `table.layout.*`; `thAction` → `th_actions`, `tdActionsContainer` → `td_actions`.
4. **Inheritance replaces boilerplate.** Set `protected ?string $parentTheme = Tailwind::class;` and declare **only the sections that differ** from Tailwind. `editable()`, `toggleable()`, and `filter()` were also reworked:
   - `editable()` keeps `clickable` / `input` / `error` **classes only**. Discard `view` — the plugin blade is `powergrid-plugins::Editable.index`.
   - `toggleable()` is now **five CSS color tokens** (`colorOn`, `colorOff`, `colorOnDark`, `colorOffDark`, `knobOn`) set via `->fill([...])`. The old `view/base/label/input/role` keys are gone.
   - `filter()` adds a `label`, a global `input`, and `dropdown`/`flyout` groups. Discard `filter.multi_select.view` — markup is `<x-livewire-powergrid::inputs.select>`.

> Token keys are snake_cased when built, so the fluent method `->thActions()` (or array key) is stored as the token `table.layout.th_actions`. You write camelCase in the fluent form; the resolved token path is snake_case. v7 also adds a `tabs` group (status tabs) with no v6 equivalent.

Full builder-class method lists, the complete mapping table, and a full worked example are in [`REFERENCE.md`](REFERENCE.md).

## Migration workflow

1. **Read the v7 base.** Open `src/Themes/Tailwind.php` — its section methods are the source of truth for the token surface. Also skim `DaisyUI.php` (a token-only subclass that ships **zero blades**) as the model to imitate. Do not rely on a memorized count -- diff against these files (see Validation).
2. **Read the v6 theme.** Collect the CSS classes from every method: `table()`, `cols()`, `footer()`, `checkbox()`, `radio()`, `editable()`, `toggleable()`, `searchBox()`, and each `filter*()`.
3. **Create `src/Themes/[ThemeName].php`** extending `\PowerComponents\LivewirePowerGrid\Themes\Theme`, `use ...\Themes\Components;`, with `protected ?string $parentTheme = Tailwind::class;` and a tiny `struct()` + `baseView()`.
4. **Add the section methods** you need (`layout()`, `header()`, `table()`, `cols()`, `footer()`, `tabs()`), translating each v6 value with the mapping table in `REFERENCE.md`. Declare only sections that differ from Tailwind.
5. **Add `editable()`, `toggleable()`, `filter()`** as separate methods (see the mini-example below).
6. **Validate** the token set against `Tailwind` (below), then update any references to the old theme class and run the tests.

## Worked mini-example (v6 Bootstrap5 -> v7)

The v7 class below shows the tiny `struct()`, `parentTheme`, and the structural
moves. The complete class (with the full `filter()`) is in [`REFERENCE.md`](REFERENCE.md).

```php
<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components;

class Bootstrap5 extends Theme
{
    // Inherit Tailwind for everything not declared below.
    protected ?string $parentTheme = Tailwind::class;

    // struct() only carries the base view.
    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())->baseView($this->baseView());
    }

    protected function baseView(): string
    {
        return 'livewire-powergrid::components.themes.bootstrap5';
    }

    /** @return array<string, mixed> */
    public function header(): array
    {
        return $this->section('header', fn (Components\Header $header) => $header
            ->view('header')
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('d-flex justify-content-between align-items-center mb-3')
                ->subContainer('d-flex gap-1')
                ->actionsContainer('d-flex align-items-center flex-wrap')
                ->actions('btn btn-outline-secondary btn-sm')   // v6 table.layout.actions
            )
            ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox   // moved under header
                ->view('header.search')
                ->input('form-control')                          // v6 searchBox.input
                ->iconClose('bi bi-x')                           // v6 searchBox.iconClose
                ->iconSearch('bi bi-search')                     // v6 searchBox.iconSearch
            )
        );
    }

    /** @return array<string, mixed> */
    public function table(): array
    {
        return $this->section('table', fn (Components\Table $table) => $table
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('my-0')                              // v6 table.layout.container
                ->table('table table-hover table-striped')       // v6 table.layout.table
                ->th('fw-bold text-secondary')                   // v6 table.header.th
                ->thActions('text-center')                       // v6 table.header.thAction (renamed)
                ->td('align-middle text-nowrap')                 // v6 table.body.td
                ->tdActions('text-end')                          // v6 table.body.tdActionsContainer (renamed)
            )
            ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox   // moved under table
                ->th('text-center')                              // v6 checkbox.th
                ->base('form-check')                             // v6 checkbox.base
                ->label('form-check-label')                      // v6 checkbox.label
                ->input('form-check-input')                      // v6 checkbox.input
            )
            ->radio(fn (Components\Radio $radio) => $radio        // moved under table
                ->th('text-center')
                ->base('form-check')
                ->label('form-check-label')
                ->input('form-check-input')
            )
        );
    }

    /** @return array<string, mixed> */
    public function footer(): array
    {
        return $this->section('footer', fn (Components\Footer $footer) => $footer
            ->view('footer')                                     // v6 footer.view (alias only)
            ->layout(fn (Components\Layout $layout) => $layout   // v6 footer keys nested under layout
                ->container('d-flex justify-content-between')    // v6 footer.footer
                ->select('form-select')                          // v6 footer.select
            )
            ->pagination('pagination')                           // v6 footer.footer_with_pagination
        );
    }

    /** @return array<string, mixed> */
    public function editable(): array
    {
        // Classes only. Plugin blade is powergrid-plugins::Editable.index — no ->view().
        return [
            'editable' => (new Components\Component())
                ->clickable('py-2')                              // NEW in v7
                ->input('form-control')
                ->error('invalid-feedback d-block')              // NEW in v7
                ->toArray(),
        ];
    }

    /** @return array<string, mixed> */
    public function toggleable(): array
    {
        // v7 toggleable is a pure CSS switch: five color tokens, no view/base/label/input/role.
        return [
            'toggleable' => (new Components\Component())
                ->fill([
                    'colorOn'      => 'var(--bs-success, #198754)',
                    'colorOff'     => 'var(--bs-gray-300, #dee2e6)',
                    'colorOnDark'  => 'var(--bs-success, #198754)',
                    'colorOffDark' => 'var(--bs-gray-600, #6c757d)',
                    'knobOn'       => 'var(--bs-white, #ffffff)',
                ])
                ->toArray(),
        ];
    }

    /** @return array<string, mixed> */
    public function filter(): array
    {
        // Abbreviated -- the full filter() (all filter types + dropdown/flyout) is in REFERENCE.md.
        return [
            'filter' => (new Components\Filter())
                ->label('form-label')
                ->boolean(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.boolean')
                    ->select('form-select')
                )
                ->inputText(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.input-text')
                    ->select('form-select')
                    ->input('form-control')
                )
                ->input('form-control')                          // NEW in v7 - global input
                ->toArray(),
        ];
    }
}
```

Any section method can equally be written as a **plain nested array** instead of
`section()`, which is often the smallest diff from v6, e.g.
`public function cols(): array { return ['cols' => ['div' => 'd-flex align-items-center gap-1']]; }`.

## Prefer tokens over new blades

DaisyUI ships **zero** Blade files — it renders entirely through Tailwind's
markup plus its own tokens, inheriting views via `parentTheme` + the
view-resolution fallback in `Theme::doResolveView()`. When migrating, only add a
Blade file (and a `->view()` token) for markup that is genuinely different for
your framework. Everything else inherits Tailwind's blade automatically.

## Validation

This is a package with no artisan console, so there is no interactive REPL to poke tokens in. Verify the token set with a Pest test that diffs the migrated theme's resolved tokens against the base `Tailwind` theme (drop it in `tests/`, run with `./vendor/bin/pest`):

```php
<?php

use Illuminate\Support\Arr;
use PowerComponents\LivewirePowerGrid\Themes\Bootstrap5;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

it('defines every token the base Tailwind theme defines', function () {
    $base     = array_keys(Arr::dot((new Tailwind())->resolveTokens()));
    $migrated = array_keys(Arr::dot((new Bootstrap5())->resolveTokens()));

    // Keys present in the base but missing from the migrated theme:
    expect(array_diff($base, $migrated))->toBe([]);
});
```

Use `resolveTokens()` (not `struct()`) so the diff covers the section methods,
`editable()`/`toggleable()`/`filter()`, and `parentTheme` inheritance together.
Because `parentTheme = Tailwind::class` fills every undeclared token, the diff
will already be empty once inheritance is set — the assertion mainly catches a
missing/misspelled key you tried to override. Fill any reported gap with the
appropriate class (or `''`).

Run the suite:

```bash
composer test               # runs ./vendor/bin/pest --compact (see composer.json scripts)
./vendor/bin/pest --filter=Theme
```

## Checklist

- [ ] `struct()` returns `ThemeBuilder::make(...)->baseView(...)` only -- the token tree lives in section methods.
- [ ] `protected ?string $parentTheme = Tailwind::class;` is declared, so only differing sections are overridden.
- [ ] Each token group is its own method (`layout`/`header`/`table`/`footer`/`cols`/`tabs`/`filter`/`editable`/`toggleable`), returning `['<group>' => [...]]` (array or via `section()`).
- [ ] `checkbox` / `radio` nested under `table`; `searchBox` nested under `header`; `footer` uses a nested `layout` + `pagination()`; `table.header.*`/`table.body.*` flattened to `table.layout.*`.
- [ ] Separate `editable()` (clickable/input/error — **no** `->view()`), `toggleable()` (five color tokens — **no** view), and `filter()` (label + global input; **no** `multi_select.view`).
- [ ] A Blade file is added only for genuinely different markup; otherwise Tailwind's view is inherited.
- [ ] `baseView()` points at `livewire-powergrid::components.themes.<name>`.
- [ ] No `public string $name` property -- `Theme::name()` derives the name from the class name.
- [ ] Token set verified against `Tailwind` via the Pest diff above (not a memorized count).
- [ ] Tests pass; no references to the old theme class remain.
