---
description: >
    Migrates a v6 Theme class to the v7 unified struct() architecture using fluent builder pattern
name: upgrade-theme-v6-to-v7
---

## What I do

- Read a legacy v6 theme class (Bootstrap, DaisyUI, a custom third-party theme) from GitHub or local disk.
- Create a v7 theme class implementing the unified `struct()` fluent builder plus the separate `editable()`, `toggleable()`, and `filter()` token methods.
- Map every legacy v6 array method (`table()`, `header()`, `checkbox()`, `searchBox()`, `filter*()`, ...) onto the correct v7 builder chain.
- Verify the migrated theme defines every token the base `Tailwind` theme defines.

This skill is the **canonical owner** of the complete v6 -> v7 mapping table, which lives in [`REFERENCE.md`](REFERENCE.md).

## When to use me

- Porting a legacy PowerGrid v6 theme to the v7 architecture.
- Upgrading a custom theme that still returns arrays from `table()`, `checkbox()`, `footer()`, etc. instead of building a `struct()`.

## How to use me

```
Use the 'upgrade-theme-v6-to-v7' skill to migrate the v6 Bootstrap theme at
https://raw.githubusercontent.com/Power-Components/livewire-powergrid/6.x/src/Themes/Bootstrap5.php
```

## The three things that change in v7

1. **Arrays become a fluent builder.** Instead of many methods each returning an array, one `struct(): Components\ThemeBuilder` builds the whole tree with typed closures.
2. **The hierarchy is re-nested:**
   - `checkbox` and `radio` move **under** `table`.
   - `searchBox` moves **under** `header`.
   - `footer` gains a nested `layout` sub-builder; the old `footer_with_pagination` becomes `pagination()`.
3. **`editable()`, `toggleable()`, and `filter()` stay separate methods** (they are merged in via `themeTokenMethods()` in `Theme.php`), and each was reworked:
   - `editable()` adds `clickable` and `error`.
   - `toggleable()` is now **five CSS color tokens** (`colorOn`, `colorOff`, `colorOnDark`, `colorOffDark`, `knobOn`) set via `->fill([...])`. The old `view/base/label/input/role` keys are gone.
   - `filter()` adds a `label` and a global `input`, and every filter type carries `view/base` (+ `select` or `input`).

> Token keys are snake_cased by the builder's `toArray()`, so the fluent method `->thActions()` is stored as the token `table.layout.th_actions`. You write camelCase; the resolved token path is snake_case.

Full builder-class method lists, the complete mapping table, and a full worked example are in [`REFERENCE.md`](REFERENCE.md).

## Migration workflow

1. **Read the v7 base.** Open `src/Themes/Tailwind.php`. Its `struct()` is the source of truth: 41 struct tokens, plus `editable()` (4), `toggleable()` (5), and `filter()` (21). Do not rely on a memorized count -- diff against this file (see Validation).
2. **Read the v6 theme.** Collect the CSS classes from every method: `table()`, `cols()`, `footer()`, `checkbox()`, `radio()`, `editable()`, `toggleable()`, `searchBox()`, and each `filter*()`.
3. **Create `src/Themes/[ThemeName].php`** extending `\PowerComponents\LivewirePowerGrid\Themes\Theme` and `use PowerComponents\LivewirePowerGrid\Themes\Components;`.
4. **Build `struct(): Components\ThemeBuilder`**, translating each v6 value with the mapping table in `REFERENCE.md`. Point `baseView()` at `livewire-powergrid::components.themes.<name>`.
5. **Add `editable()`, `toggleable()`, `filter()`** as separate methods (see the mini-example below).
6. **Validate** the token set against `Tailwind` (below), then update any references to the old theme class and run the tests.

## Worked mini-example (v6 Bootstrap5 -> v7)

The `struct()` below shows all three structural moves. The complete class (with the full `filter()`) is in [`REFERENCE.md`](REFERENCE.md).

```php
<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components;

class Bootstrap5 extends Theme
{
    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.themes.bootstrap5')
            ->layout(fn (Components\Layout $layout) => $layout
                ->wrapper('')
                ->outsideFilters('')
            )
            ->header(fn (Components\Header $header) => $header
                ->view('header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('d-flex justify-content-between align-items-center mb-3')
                    ->subContainer('d-flex gap-1')
                    ->actionsContainer('d-flex align-items-center flex-wrap')
                    ->actions('btn btn-outline-secondary btn-sm')   // v6 table.layout.actions
                )
                ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox   // moved under header
                    ->view('header.search')
                    ->container('d-flex justify-content-end')
                    ->relativeMain('position-relative')
                    ->input('form-control')                          // v6 searchBox.input
                    ->iconSearchWrapper('position-absolute top-50 start-0 translate-middle-y ps-2')
                    ->iconCloseWrapper('position-absolute top-50 end-0 translate-middle-y pe-2')
                    ->iconClose('bi bi-x')                           // v6 searchBox.iconClose
                    ->iconSearch('bi bi-search')                     // v6 searchBox.iconSearch
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('my-0')                              // v6 table.layout.container
                    ->table('table table-hover table-striped')      // v6 table.layout.table
                    ->thead('')                                      // v6 table.header.thead
                    ->tr('')                                         // v6 table.header.tr
                    ->th('fw-bold text-secondary')                   // v6 table.header.th
                    ->thActions('text-center')                       // v6 table.header.thAction (renamed)
                    ->tbody('')                                      // v6 table.body.tbody
                    ->td('align-middle text-nowrap')                 // v6 table.body.td
                    ->tdActions('text-end')                          // v6 table.body.tdActionsContainer (renamed)
                )
                ->body(fn (Components\Body $body) => $body
                    ->tr(fn (Components\Tr $tr) => $tr
                        ->responsive('')                             // NEW in v7
                        ->responsiveToggleIcon('')                   // NEW in v7
                    )
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
            )
            ->cols(fn (Components\Cols $cols) => $cols
                ->div('d-flex align-items-center gap-1')             // v6 cols.div
            )
            ->footer(fn (Components\Footer $footer) => $footer
                ->view('footer')                                     // v6 footer.view
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
        return [
            'editable' => (new Components\Component())
                ->view('livewire-powergrid::components.themes.bootstrap5.editable')
                ->clickable('py-2')                                  // NEW in v7
                ->input('form-control')
                ->error('invalid-feedback d-block')                  // NEW in v7
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
        // Abbreviated -- the full filter() (all six filter types) is in REFERENCE.md.
        return [
            'filter' => (new Components\Filter())
                ->label('form-label')
                ->boolean(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.boolean')
                    ->base('')
                    ->select('form-select')
                )
                ->inputText(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.input-text')
                    ->base('')
                    ->select('form-select')
                    ->input('form-control')
                )
                ->input('form-control')                              // NEW in v7 - global input
                ->toArray(),
        ];
    }
}
```

## Validation

This is a package with no artisan console, so there is no interactive REPL to poke tokens in. Verify the token set with a Pest test that diffs the migrated theme's keys against the base `Tailwind` theme (drop it in `tests/`, run with `./vendor/bin/pest`):

```php
<?php

use Illuminate\Support\Arr;
use PowerComponents\LivewirePowerGrid\Themes\Bootstrap5;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

it('defines every token the base Tailwind theme defines', function () {
    $base    = array_keys(Arr::dot((new Tailwind())->struct()->toArray()));
    $migrated = array_keys(Arr::dot((new Bootstrap5())->struct()->toArray()));

    // Keys present in the base but missing from the migrated theme:
    expect(array_diff($base, $migrated))->toBe([]);
});
```

`Arr::dot(...)` flattens the builder output to dot-notation leaf keys (e.g. `table.layout.th_actions`), and `array_diff` reports any base key the migrated theme forgot. Extend the same assertion to `->resolveTokens()` if you also want to cover the merged `editable()`/`toggleable()`/`filter()` tokens. Fill any reported gap with the appropriate class (or `''`).

Run the suite:

```bash
composer test               # runs ./vendor/bin/pest --compact (see composer.json scripts)
./vendor/bin/pest --filter=Theme
```

## Checklist

- [ ] `struct()` reproduces every token the base `Tailwind` `struct()` defines (verified by the Pest diff above, not a memorized count).
- [ ] Fluent builder with typed closures -- no arrays returned from `struct()`.
- [ ] `checkbox` / `radio` nested under `table`; `searchBox` nested under `header`; `footer` uses a nested `layout` + `pagination()`.
- [ ] New v7 struct tokens present (`layout.wrapper`, `header.*`, `table.body.tr.responsive*`).
- [ ] Separate `editable()` (with `clickable` + `error`), `toggleable()` (five color tokens), and `filter()` (with `label` + global `input`) methods.
- [ ] `baseView()` points at `livewire-powergrid::components.themes.<name>`.
- [ ] No `public string $name` property -- `Theme::name()` derives the name from the class name.
- [ ] Tests pass; no references to the old theme class remain.
