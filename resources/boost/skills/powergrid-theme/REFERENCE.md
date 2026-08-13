# PowerGrid v7 Theme Reference

Companion to `SKILL.md`. Everything here is verified against the v7 source in
`src/Themes/`. When in doubt, read `Tailwind.php` (full map), `DaisyUI.php`
(lean subclass), and `Flux.php` (subclass that overrides `resolveTokens()`).

---

## Key files

| File | Role |
|---|---|
| `src/Themes/Theme.php` | Abstract base. `struct()` is abstract; provides `resolveTokens()`, `resolveView()`, `merge()`, `get()`, and the default `filter()`/`editable()`/`toggleable()` (all `[]`). `protected ?string $parentTheme = null;` |
| `src/Themes/Tailwind.php` | Base theme. Full `struct()`, `filter()`, `editable()`, `toggleable()`. `parentTheme = null`. |
| `src/Themes/DaisyUI.php` | Lean subclass. `parentTheme = Tailwind::class`. |
| `src/Themes/Flux.php` | Subclass that also overrides `resolveTokens()` to append pagination tokens. `parentTheme = Tailwind::class`. |
| `src/Themes/Components/ThemeBuilder.php` | Root fluent builder returned by `struct()`. Sections: `baseView`, `layout`, `header`, `table`, `footer`, `cols`. |
| `src/Themes/Components/Layout.php` | CSS-class group used inside every section's `->layout(Closure)`. |
| `src/Themes/Components/Header.php` | `view()`, `layout()`, `searchBox()`. |
| `src/Themes/Components/SearchBox.php` | `view`, `container`, `relativeMain`, `input`, `iconSearchWrapper`, `iconCloseWrapper`, `iconClose`, `iconSearch`. |
| `src/Themes/Components/Table.php` | `layout()`, `body()`, `checkbox()`, `radio()`, plus optional `view*()` alias setters. |
| `src/Themes/Components/Body.php` | `tr(Closure\|string)`, `td(Closure\|string)`. |
| `src/Themes/Components/Tr.php` | `base`, `responsive`, `responsiveToggleIcon`. |
| `src/Themes/Components/Td.php` | `base`, `actionsWrapper`. |
| `src/Themes/Components/Checkbox.php` | `th`, `base`, `label`, `input`. |
| `src/Themes/Components/Radio.php` | `th`, `base`, `label`, `input`. |
| `src/Themes/Components/Cols.php` | `div`. |
| `src/Themes/Components/Footer.php` | `view()`, `layout()`, `pagination(Closure\|array\|string)`. |
| `src/Themes/Components/Pagination.php` | Holds the pagination `view`; usually set via the string form. |
| `src/Themes/Components/Filter.php` | Fluent filter builder (used by DaisyUI/Flux `filter()`). |
| `src/Themes/Components/Flyout.php` | Classes for the filter drawer, via `Filter::flyout(Closure)`. `view`, `overlay`, `panel`, `panelLeft`, `panelRight`, `header`, `title`, `close`, `body`, `footer`, `clearAll`. |
| `src/Themes/Components/Component.php` | Generic sub-component (used in `filter()` array form, `editable()`, `toggleable()`). |
| `src/Themes/Components/HasProperties.php` | Shared trait. `toArray()` snake_cases keys and prefixes `view*` values with `baseView`. |
| `src/Support/ThemeManager.php` | `ThemeManager::theme($key, $default)` and `::view($alias)` — read `app('powergrid.theme')`. |
| `src/functions.php` | Global helpers `theme($key, $default)` and `theme_view($alias)`. |
| `src/PowerGridComponent.php` | `boot()` resolves the theme (`customThemeClass()` → config, then `template()`) and binds `powergrid.theme`. |
| `resources/config/livewire-powergrid.php` | `'theme' => Tailwind::class` (default). |

All builder classes are in namespace `PowerComponents\LivewirePowerGrid\Themes\Components`. Inside a `Themes\` class, reference them as `Components\ClassName`.

---

## Complete `struct()` token map

Full shape with the correct closure type-hints, string pagination, and a
`themes` base view. Every method name and hint below matches the source.

```php
public function struct(): Components\ThemeBuilder
{
    return Components\ThemeBuilder::make($this->name())
        ->baseView('livewire-powergrid::components.themes.my-theme') // real folder: resources/views/components/themes/*
        ->layout(fn (Components\Layout $layout) => $layout
            ->wrapper('')                                 // outermost grid wrapper
            ->outsideFilters('')                          // container for filters rendered outside the table
        )
        ->header(fn (Components\Header $header) => $header
            ->view('header')                              // alias, prefixed with baseView when it has no '::'
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('')                           // outer header wrapper
                ->subContainer('')                        // inner flex wrapper
                ->actionsContainer('')                    // wraps header action buttons
                ->actions('')                             // action button classes
            )
            ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                ->view('header.search')                   // alias
                ->container('')                           // search-box outer wrapper
                ->relativeMain('')                        // relative-positioned inner wrapper
                ->input('')                               // text input classes
                ->iconSearchWrapper('')                   // wrapper around the search icon
                ->iconSearch('')                          // search icon classes
                ->iconCloseWrapper('')                    // wrapper around the clear icon
                ->iconClose('')                           // clear icon classes
            )
        )
        ->table(fn (Components\Table $table) => $table
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('')                           // outer <table> wrapper (scroll container)
                ->table('')                               // <table> classes
                ->thead('')                               // <thead> classes
                ->tr('')                                  // header <tr> classes
                ->trStriped('')                           // striped row variant (optional)
                ->trNotStriped('')                        // non-striped row variant (optional)
                ->th('')                                  // <th> classes
                ->thActions('')                           // actions-column <th> classes
                ->tbody('')                               // <tbody> classes
                ->td('')                                  // <td> classes
                ->tdActions('')                           // actions-column <td> classes
            )
            ->body(fn (Components\Body $body) => $body
                ->tr(fn (Components\Tr $tr) => $tr
                    ->responsive('')                      // responsive/detail row classes
                    ->responsiveToggleIcon('')            // expand/collapse icon classes
                )
            )
            ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox
                ->th('')                                  // checkbox column <th> classes
                ->base('')                                // wrapper around the checkbox
                ->label('')                               // <label> classes
                ->input('')                               // <input type="checkbox"> classes
            )
            ->radio(fn (Components\Radio $radio) => $radio
                ->th('')                                  // radio column <th> classes
                ->base('')                                // wrapper around the radio
                ->label('')                               // <label> classes
                ->input('')                               // <input type="radio"> classes
            )
        )
        ->cols(fn (Components\Cols $cols) => $cols
            ->div('')                                     // per-column header cell inner wrapper
        )
        ->footer(fn (Components\Footer $footer) => $footer
            ->view('footer')                              // alias
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('')                           // footer wrapper classes
                ->select('')                              // per-page <select> classes
            )
            ->pagination('pagination')                    // STRING alias — not a closure
        );
}
```

### Optional `Table` view-alias setters

`Table` also exposes setters that override the auto-resolved view for a
specific table part. Declare them only when your theme ships its own Blade file
for that part; otherwise the `baseView + alias` fallback (then the parent chain)
resolves it:

`viewLayout`, `viewHeader`, `viewRow`, `viewCols`, `viewThEmpty`,
`viewInlineFilters`, `viewCheckboxAll`, `viewCheckboxRow`, `viewRadioRow`.

Example: `->table(fn (Components\Table $table) => $table->viewRow('table.row')->layout(...))`.

---

## `filter()` — token map

`filter()` returns `['filter' => [...]]`. The base `Tailwind.php` writes it as a
plain nested array — the simplest copy-paste-correct form. Keep the `::`-qualified
filter view paths so they resolve regardless of your theme's `baseView`:

```php
public function filter(): array
{
    return [
        'filter' => [
            'label' => 'block text-sm font-medium text-zinc-700 dark:text-zinc-300',
            'boolean' => [
                'view'   => 'livewire-powergrid::components.themes.tailwind.filters.boolean',
                'base'   => 'min-w-[5rem]',
                'select' => '...select classes...',
            ],
            'date_picker' => [
                'view'  => 'powergrid-plugins::Flatpickr.index',
                'base'  => '',
                'input' => '...input classes...',
            ],
            'multi_select' => [
                'view'   => 'livewire-powergrid::components.themes.tailwind.filters.multi-select',
                'base'   => 'inline-block relative w-full',
                'select' => 'mt-1',
            ],
            'number' => [
                'view'  => 'livewire-powergrid::components.themes.tailwind.filters.number',
                'base'  => '',
                'input' => '...input classes...',
            ],
            'select' => [
                'view'   => 'livewire-powergrid::components.themes.tailwind.filters.select',
                'base'   => '',
                'select' => '...select classes...',
            ],
            'input_text' => [
                'view'   => 'livewire-powergrid::components.themes.tailwind.filters.input-text',
                'base'   => 'min-w-[9.5rem]',
                'select' => '...select classes...',
                'input'  => '...input classes...',
            ],
            'input' => '...generic filter input classes...',
            'flyout' => [
                'view'        => 'livewire-powergrid::components.themes.tailwind.filter-flyout',
                'overlay'     => '...backdrop classes...',
                'panel'       => '...classes shared by both drawer sides...',
                'panel_left'  => '...anchoring classes when position is left...',
                'panel_right' => '...anchoring classes when position is right...',
                'header'      => '...drawer header row...',
                'title'       => '...drawer title...',
                'close'       => '...close button...',
                'body'        => '...scrollable filter area...',
                'footer'      => '...drawer footer...',
                'clear_all'   => '...clear all filters button...',
            ],
        ],
    ];
}
```

**Fluent alternative.** DaisyUI and Flux build the same map with the
`Components\Filter` builder (`->label(...)->boolean(fn ($c) => $c->view(...)->select(...))-> ... ->toArray()`).
It is equivalent — pick whichever you find clearer, and copy the exact form from
`src/Themes/DaisyUI.php` if you want the fluent version.

### `filter.flyout` — the drawer

`filter.flyout.*` styles the drawer used when `config('livewire-powergrid.filter')`
is `flyout`. Its blade lives at
`resources/views/components/themes/tailwind/filter-flyout.blade.php` and is shared
by every theme through `parentTheme`, so a theme normally overrides the classes
only, not `view`. Use the fluent `->flyout(fn (Components\Flyout $flyout) => ...)`
setter (`Components\Flyout`) or the plain array form above.

Two constraints the drawer markup relies on:

- `panel` must position the drawer itself (`fixed inset-y-0`) and set a stacking
  context above `overlay`. `panel_left` / `panel_right` add only the edge
  anchoring, because the blade picks one of the two based on the configured
  position and pairs it with a matching slide-in transform.
- `panel` should be full width on small screens and constrained above the `sm`
  breakpoint (`w-full max-w-full sm:w-96 sm:max-w-[90vw]`).

Because a subclass with `parentTheme = Tailwind::class` inherits Tailwind's
`filter()` wholesale, you only need to override `filter()` when your framework's
inputs need different classes or views.

---

## `editable()` — token map

Returns `['editable' => [...]]`. Uses the generic `Components\Component`, with a
real `editable` Blade view (Tailwind ships one; subclasses reuse it):

```php
public function editable(): array
{
    return [
        'editable' => (new Components\Component())
            ->view('livewire-powergrid::components.themes.tailwind.editable')
            ->clickable('py-2')                           // wrapper shown before entering edit mode
            ->input('...input classes...')                // the edit <input>
            ->error('text-sm text-red-800 p-1 transition-all duration-200')
            ->toArray(),
    ];
}
```

---

## `toggleable()` — color tokens (NO view)

`toggleable()` does **not** reference a Blade view. It fills five color tokens
that the shipped Toggleable switch blade (`src/Plugins/Toggleable/index.blade.php`)
reads via `theme('toggleable.color_on')`, `theme('toggleable.color_off')`,
`theme('toggleable.color_on_dark')`, `theme('toggleable.color_off_dark')`, and
`theme('toggleable.knob_on')`. Copy this exact shape from `Tailwind.php`:

```php
public function toggleable(): array
{
    return [
        'toggleable' => (new Components\Component())
            ->fill([
                'colorOn'      => 'var(--color-accent, #16a34a)',
                'colorOff'     => 'var(--color-zinc-200, #e4e4e7)',
                'colorOnDark'  => 'var(--color-accent, #16a34a)',
                'colorOffDark' => 'var(--color-zinc-600, #52525b)',
                'knobOn'       => 'var(--color-accent-foreground, #ffffff)',
            ])
            ->toArray(),
    ];
}
```

`HasProperties::toArray()` snake_cases the keys, so `colorOn → color_on`,
`colorOnDark → color_on_dark`, `knobOn → knob_on`, etc. — which is why the blade
reads them as `toggleable.color_on`.

---

## Dot-notation token access

Tokens are read with `data_get()` dot notation, in PHP or Blade:

```php
// PHP
ThemeManager::theme('table.layout.td');
ThemeManager::theme('filter.boolean.select', 'fallback-class');

// Global helpers (Blade or PHP)
theme('layout.wrapper');
theme('header.layout.container');
theme('header.search_box.input');   // note: snake_case (searchBox → search_box)
theme('table.layout.table');
theme('table.checkbox.input');
theme('table.radio.input');
theme('table.body.tr.responsive');
theme('cols.div');
theme('footer.layout.select');
theme('filter.boolean.select');
theme('toggleable.color_on');

theme_view('pagination');           // resolve a Blade view path by alias
theme_view('header.search');
```

All keys are snake_cased by `HasProperties::toArray()`: a builder method like
`->subContainer(...)` becomes the token `sub_container`, `->searchBox(...)`
becomes `search_box`, `->trStriped(...)` becomes `tr_striped`, and so on.

---

## Token resolution order (`Theme::resolveTokens()`)

Built once and cached in `$this->tokens`, in this order:

1. `struct()` — the current theme's builder, converted with `toArray()`.
2. **Parent theme** — if `parentTheme` is set and not `static::class`, the parent's
   fully-resolved tokens are computed and the current tokens are merged **on top**
   (`array_replace_recursive`). This is how a subclass inherits Tailwind and
   overrides only what it declares.
3. **Theme token methods** — `filter()`, `editable()`, `toggleable()` (the list from
   `themeTokenMethods()`) are each merged in if the method exists and returns a
   non-empty array.
4. **Plugin tokens** — `themeTokens()` contributed by registered plugins.

`Flux.php` additionally overrides `resolveTokens()` to call `parent::resolveTokens()`
and then append extra `table.body.td.actions_wrapper` and `pagination.*` tokens —
a valid pattern when a few tokens are easier to add imperatively.

---

## View resolution order (`Theme::resolveView($alias)`)

Cached per `class::alias`. For a given alias:

1. Explicit `views()` map on the theme (base returns `[]`; override to hard-map an alias → view).
2. Token lookup: the alias as a token, then `view_<alias>`, then `<alias>.view`, then the `search_box` special case, then a scan of `header`/`table`/`footer` for `view_<alias>` / `<alias>.view`.
3. If a token view string was found but does not exist and has no `::`, retry it prefixed with `baseView`.
4. **`baseView + '.' + alias`** fallback — `baseView.<alias>` (dots) or the dashed variant, if `view()->exists()`.
5. Delegate to `parentTheme->resolveView($alias)` (ultimately Tailwind).
6. Final fallback: `livewire-powergrid::components.structure.<alias>`.

Consequences worth remembering:

- Feature views (export, toggle-columns, soft-deletes, summarize, detail, etc.)
  need **no** entry in `struct()` — they resolve by `baseView + alias`, then the
  parent chain, then `components.structure.*`.
- Only declare `->view()` on a section/sub-component when your theme actually
  ships that Blade file. Omit it to inherit the parent's view.
- A view value containing `::` is used verbatim; a bare value is treated as an
  alias and prefixed with `baseView` by `HasProperties::toArray()`.
