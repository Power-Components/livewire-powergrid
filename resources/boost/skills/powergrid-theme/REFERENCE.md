# PowerGrid v7 Theme Reference

Companion to `SKILL.md`. Everything here is verified against the v7 source in
`src/Themes/`. **`src/Themes/Tailwind.php` is the source of truth for the token
surface** — read it (and `DaisyUI.php` / `Flux.php`) rather than trusting any
hand-written list here if the source has changed.

---

## Key files

| File | Role |
|---|---|
| `src/Themes/Theme.php` | Abstract base. `struct()` is abstract (`array\|ThemeBuilder`). Provides `resolveTokens()`, `resolveView()`, `doResolveView()`, `merge()`, `get()`, `section()`, `themeTokenMethods()`, and the default `filter()`/`editable()`/`toggleable()` (all `[]`). `protected ?string $parentTheme = null;` |
| `src/Themes/Tailwind.php` | Base theme. Tiny `struct()` (baseView only) plus section methods: `layout()`, `header()`, `table()`, `cols()`, `footer()`, `tabs()`, `filter()`, `editable()`, `toggleable()`. `parentTheme = null`. |
| `src/Themes/DaisyUI.php` | Token-only subclass. `parentTheme = Tailwind::class`. Ships **zero blades** — inherits Tailwind's markup. |
| `src/Themes/Flux.php` | Subclass keeping its own `<flux:*>` blades where the HTML differs (e.g. `tabs.view` → `powergrid-plugins::Tabs.themes.flux`). `parentTheme = Tailwind::class`. |
| `src/Themes/ArrayTheme.php` | Data-first theme: author tokens as a plain array. `fromArray()`, `fromFile()`, or subclass + `struct(): array`. |
| `src/PowerGridManager.php` | Theme registry: `$themes` / `DEFAULT_THEMES`, `registerTheme($name, $class)`, `resolveThemeClass($nameOrClass)`. |
| `src/Themes/Components/ThemeBuilder.php` | Root fluent builder. Methods: `make()`, `baseView`, `layout`, `header`, `table`, `footer`, `cols`, `tabs`. |
| `src/Themes/Components/Layout.php` | CSS-class group used inside every section's `->layout(Closure)`. |
| `src/Themes/Components/Header.php` | `view()`, `layout()`, `searchBox()`, plus the header-control buttons `toggleColumns()`, `softDeletes()`, `filters()`, `filterBuilder()`, `export()`, `enabledFilters()` (each takes a `HeaderButton` closure). |
| `src/Themes/Components/HeaderButton.php` | Header-control styling: `button`, `icon`, `iconClass`, `label`, `menu`, `menuItem`, `panel`, `badge`, `wrapper`, `pill`, `pillClearAll`, `view`. |
| `src/Themes/Components/SearchBox.php` | `view`, `container`, `relativeMain`, `input`, `iconSearchWrapper`, `iconCloseWrapper`, `iconClose`, `iconSearch`, `icon`, `iconClear`. |
| `src/Themes/Components/Table.php` | `layout()`, `body()`, `checkbox()`, `radio()`, plus optional `view*()` alias setters. |
| `src/Themes/Components/Body.php` | `tr(Closure\|string)`, `td(Closure\|string)`. |
| `src/Themes/Components/Tr.php` | `base`, `responsive`, `responsiveToggleIcon`. |
| `src/Themes/Components/Td.php` | `base`, `actionsWrapper`. |
| `src/Themes/Components/Checkbox.php` | `th`, `base`, `label`, `input`. |
| `src/Themes/Components/Radio.php` | `th`, `base`, `label`, `input`. |
| `src/Themes/Components/Cols.php` | `div`. |
| `src/Themes/Components/Tabs.php` | `view`, `list`, `tab`, `tabActive`, `tabInactive`, `badge`, `badgeActive`, `badgeInactive`. |
| `src/Themes/Components/Footer.php` | `view()`, `layout()`, `pagination(Closure\|array\|string)`. |
| `src/Themes/Components/Pagination.php` | Holds the pagination `view`; usually set via the string form. |
| `src/Themes/Components/Filter.php` | Fluent filter builder: `label`, `input`, `boolean`, `datePicker`, `multiSelect`, `number`, `select`, `inputText`, `dropdown`, `flyout`. |
| `src/Themes/Components/Dropdown.php` | Classes for the filter dropdown popover, via `Filter::dropdown(Closure)`. |
| `src/Themes/Components/Flyout.php` | Classes for the filter drawer, via `Filter::flyout(Closure)`. |
| `src/Themes/Components/Component.php` | Generic sub-component (used in `editable()`, `toggleable()`, and array-form filter types). |
| `src/Themes/Components/HasProperties.php` | Shared trait. `toArray()` snake_cases keys and prefixes `view*` values with `baseView`. `setBaseView()`, `fill()`. |
| `src/functions.php` | Global helpers `theme($key, $default)` and `theme_view($alias)`. |
| `src/PowerGridComponent.php` | `template()` per-component override; `boot()` resolves and binds `powergrid.theme`. |
| `src/Concerns/Base.php` | `customThemeClass()` per-component override. |
| `resources/config/livewire-powergrid.php` | `'theme' => 'tailwind'` (name or FQCN) and `'theme_overrides' => []` (no-code token overrides). |

All builder classes are in namespace `PowerComponents\LivewirePowerGrid\Themes\Components`. Inside a `Themes\` class, reference them as `Components\ClassName`.

---

## `struct()` is tiny — section methods carry the tokens

`struct()` no longer holds the whole token tree. In every shipped theme it only
sets the base view:

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

Each token group is a separate **public method** returning that group's slice.
The list comes from `Theme::themeTokenMethods()`:

```
['layout', 'header', 'table', 'footer', 'cols', 'tabs', 'filter', 'editable', 'toggleable']
```

Write a section method either as a plain nested array or with the `section()`
helper (identical result; `section()` prefixes `view*` tokens with `baseView`):

```php
// Array form
public function cols(): array
{
    return ['cols' => ['div' => 'flex items-center gap-1']];
}

// Fluent form via section()
public function cols(): array
{
    return $this->section('cols', fn (Components\Cols $cols) => $cols->div('flex items-center gap-1'));
}
```

---

## Token surface (from `src/Themes/Tailwind.php`)

Read `Tailwind.php` for the exact class strings; this maps the **method → token**
shape. Every leaf is snake_cased by `HasProperties::toArray()`.

### `layout()`
`wrapper`, `card`, `outsideFilters` → `layout.wrapper`, `layout.card`, `layout.outside_filters`

### `header()`
- `view` → `header.view`
- `->layout(fn (Components\Layout $l) => ...)`: `container`, `subContainer`, `actionsContainer`, `actions` → `header.layout.*`
- `->searchBox(fn (Components\SearchBox $s) => ...)`: `view`, `container`, `relativeMain`, `input`, `iconSearchWrapper`, `iconCloseWrapper`, `iconClose`, `iconSearch`, `icon`, `iconClear` → `header.search_box.*`
- `->toggleColumns(fn (Components\HeaderButton $b) => ...)`: `button`, `iconClass`, `label`, `menu`, `menuItem`, `panel` → `header.toggle_columns.*`
- `->softDeletes(fn (Components\HeaderButton $b) => ...)`: `button`, `iconClass`, `label`, `menu`, `menuItem` → `header.soft_deletes.*`
- `->filters(fn (Components\HeaderButton $b) => ...)`: `wrapper`, `button`, `iconClass`, `label` → `header.filters.*`
- `->filterBuilder(fn (Components\HeaderButton $b) => ...)`: `button`, `iconClass`, `label`, `badge` → `header.filter_builder.*`
- `->export(fn (Components\HeaderButton $b) => ...)`: `button`, `iconClass`, `label`, `menu`, `menuItem` → `header.export.*`
- `->enabledFilters(fn (Components\HeaderButton $b) => ...)`: `wrapper`, `label`, `iconClass`, `pill`, `pillClearAll` → `header.enabled_filters.*`

### `table()`
- `->layout(...)`: `container`, `table`, `thead`, `tr`, `theadTr`, `emptyState`, `th`, `thActions`, `tbody`, `td`, `tdActions` → `table.layout.*` (DaisyUI additionally uses `trStriped`/`trNotStriped`)
- `->body(fn (Components\Body $b) => $b->tr(fn (Components\Tr $tr) => ...))`: `responsive`, `responsiveToggleIcon` → `table.body.tr.*`
- `->checkbox(fn (Components\Checkbox $c) => ...)`: `th`, `base`, `label`, `input` → `table.checkbox.*`
- `->radio(fn (Components\Radio $r) => ...)`: `th`, `base`, `label`, `input` → `table.radio.*`

### `cols()`
`div` → `cols.div`

### `footer()`
- `view` → `footer.view`
- `->layout(...)`: `container`, `select` → `footer.layout.*`
- `->pagination('pagination')` → `footer.pagination.view`

### `tabs()`
`view` (optional), `list`, `tab`, `tabActive`, `tabInactive`, `badge`, `badgeActive`, `badgeInactive` → `tabs.*`

```php
public function tabs(): array
{
    return $this->section('tabs', fn (Components\Tabs $tabs) => $tabs
        ->list('inline-flex items-center gap-1 rounded-xl border p-1')
        ->tab('rounded-lg px-3 py-1.5 text-sm font-medium transition')
        ->tabActive('bg-gray-100 text-gray-900 shadow-sm')
        ->tabInactive('text-gray-500 hover:text-gray-800')
        ->badge('rounded-full px-2 py-0.5 text-xs font-semibold')
        ->badgeActive('bg-blue-100 text-blue-700')
        ->badgeInactive('bg-gray-100 text-gray-600')
    );
}
```

The tabs view resolves via `theme_view('tabs')` (`src/Concerns/HasTabs.php`). The
shared base blade `resources/views/components/themes/tailwind/tabs.blade.php`
reads these tokens and renders tab icons via `IconRenderer`. Set `->view(...)`
only when the markup differs (Flux → `powergrid-plugins::Tabs.themes.flux`);
Tailwind and DaisyUI share the base blade.

### Optional `Table` view-alias setters

`Table` also exposes setters that override the auto-resolved view for a specific
table part. Declare them only when your theme ships its own Blade file for that
part; otherwise the `baseView + alias` fallback (then the parent chain) resolves
it: `view`, `viewLayout`, `viewHeader`, `viewRow`, `viewCols`, `viewThEmpty`,
`viewInlineFilters`, `viewCheckboxAll`, `viewCheckboxRow`, `viewRadioRow`.

---

## `filter()` — token map

`filter()` returns `['filter' => [...]]`. `Tailwind.php` writes it as a plain
nested array; `DaisyUI.php` / `Flux.php` build the same map with the fluent
`Components\Filter` builder. Keep the `::`-qualified filter view paths so they
resolve regardless of your theme's `baseView`.

Filter types and their leaves (verify classes against `Tailwind.php`):

- `label` → `filter.label`
- `boolean`: `view`, `base`, `select`
- `date_picker`: `view` (base points at `powergrid-plugins::Flatpickr.index`), `base`, `input`
- `multi_select`: `view`, `base`, `select`
- `number`: `view`, `base`, `input`
- `select`: `view`, `base`, `select`
- `input_text`: `view`, `base`, `select`, `input`
- `input` → `filter.input` (global input styling)
- `dropdown`: `view`, `wrapper`, `trigger`, `badge`, `panel`, `header`, `title`, `body`, `grid`, `footer`, `reset`, `clear`, `apply` — the popover shown when `config('livewire-powergrid.filter')` is `dropdown`
- `flyout`: `view`, `overlay`, `panel`, `panel_left`, `panel_right`, `header`, `title`, `close`, `body`, `footer`, `clear_all` — the drawer shown when `config('livewire-powergrid.filter')` is `flyout`

Fluent form (as in DaisyUI/Flux):

```php
public function filter(): array
{
    return [
        'filter' => (new Components\Filter())
            ->label('...')
            ->boolean(fn (Components\Component $c) => $c->view('...')->select('...'))
            // ...datePicker/multiSelect/number/select/inputText...
            ->input('...')
            ->dropdown(fn (Components\Dropdown $d) => $d->wrapper('...')->panel('...')->apply('...'))
            ->flyout(fn (Components\Flyout $f) => $f->overlay('...')->panel('...')->panelLeft('...'))
            ->toArray(),
    ];
}
```

### `filter.dropdown` / `filter.flyout` — the popover and drawer

Both styles are shared by every theme through `parentTheme`, so a subclass
normally overrides the **classes** only, not `view`. Two constraints the drawer
markup relies on:

- `panel` must position the drawer itself (`fixed inset-y-0`) and set a stacking
  context above `overlay`. `panel_left` / `panel_right` add only the edge
  anchoring; the blade picks one based on the configured position and pairs it
  with a matching slide-in transform.
- `panel` should be full width on small screens and constrained above the `sm`
  breakpoint (`w-full max-w-full sm:w-96 sm:max-w-[90vw]`).

A subclass with `parentTheme = Tailwind::class` inherits Tailwind's `filter()`
wholesale, so override `filter()` only when your framework's inputs need
different classes or views.

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
reads via `theme('toggleable.color_on')`, `color_off`, `color_on_dark`,
`color_off_dark`, and `knob_on`:

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
`colorOnDark → color_on_dark`, `knobOn → knob_on`.

---

## No-code overrides — `config('livewire-powergrid.theme_overrides')`

A nested token array in `resources/config/livewire-powergrid.php`, merged **last**
(highest precedence) in `resolveTokens()`. Restyle any token without a Theme class:

Keys must already be snake_cased — overrides are merged after `HasProperties::toArray()`, so `tabActive` would miss `theme('tabs.tab_active')`.

```php
'theme_overrides' => [
    'table' => ['layout' => ['th' => 'font-bold px-4 py-3']],
    'tabs'  => ['tab_active' => 'bg-emerald-100 text-emerald-800'],
],
```

---

## Theme registry — select by name

`config('livewire-powergrid.theme')` accepts a registered **name** or an FQCN:

```php
'theme' => 'daisyui',                                   // registered name
// 'theme' => \PowerComponents\LivewirePowerGrid\Themes\Flux::class, // FQCN
```

Default names live in `PowerGridManager::DEFAULT_THEMES`
(`tailwind` / `daisyui` / `flux`). Register your own (e.g. from a satellite
package's service provider):

```php
use PowerComponents\LivewirePowerGrid\PowerGridManager;

PowerGridManager::registerTheme('bootstrap', BootstrapTheme::class);
```

`PowerGridManager::resolveThemeClass($nameOrClass)` maps a name to its FQCN
(or returns the string unchanged when it is already a class).

---

## `ArrayTheme` — data-first theme

`src/Themes/ArrayTheme.php` authors a theme as a plain nested token array;
undeclared tokens fall back to the parent theme (Tailwind by default).

```php
// Ad-hoc:
ArrayTheme::fromArray($tokens, parentTheme: Tailwind::class, name: 'my-theme');
ArrayTheme::fromFile(base_path('themes/my-theme.php'), parentTheme: Tailwind::class);

// Subclass:
class MyTheme extends ArrayTheme
{
    protected ?string $parentTheme = Tailwind::class;

    public function struct(): array
    {
        return ['footer' => ['pagination' => ['item' => 'btn ...']]];
    }
}
```

---

## Dot-notation token access

Tokens are read with `data_get()` dot notation, in PHP or Blade:

```php
theme('layout.card');
theme('header.layout.container');
theme('header.search_box.input');   // snake_case (searchBox → search_box)
theme('table.layout.table');
theme('table.checkbox.input');
theme('table.body.tr.responsive');
theme('cols.div');
theme('tabs.tab_active');            // tabActive → tab_active
theme('footer.layout.select');
theme('filter.boolean.select');
theme('filter.dropdown.panel');
theme('toggleable.color_on');

theme_view('pagination');           // resolve a Blade view path by alias
theme_view('tabs');
theme_view('header.search');
```

All keys are snake_cased by `HasProperties::toArray()`: `->subContainer(...)` →
`sub_container`, `->searchBox(...)` → `search_box`, `->tabActive(...)` →
`tab_active`, and so on.

---

## Token resolution order (`Theme::resolveTokens()`)

Built once and cached in `$this->tokens`, in this order:

1. `struct()` — the current theme's builder (baseView only), converted with `toArray()` (or the array for `ArrayTheme`).
2. **Parent theme** — if `parentTheme` is set and not `static::class`, the parent's fully-resolved tokens are computed and the current tokens are merged **on top** (`array_replace_recursive`). This is how a subclass inherits Tailwind.
3. **Theme token methods** — each of `themeTokenMethods()` (`layout`, `header`, `table`, `footer`, `cols`, `tabs`, `filter`, `editable`, `toggleable`) is merged in if the method exists and returns a non-empty array.
4. **Plugin tokens** — `PluginBase::themeTokens()` from every registered plugin.
5. **Config overrides** — `config('livewire-powergrid.theme_overrides')`, merged **last** so it wins over everything.

---

## View resolution order (`Theme::doResolveView($alias)`)

Cached per `class::alias`. For a given alias:

1. Explicit `views()` map on the theme (base returns `[]`; override to hard-map an alias → view).
2. Token lookup: the alias as a token, then `view_<alias>`, then `<alias>.view`, then the `search_box` special case, then a scan of `header`/`table`/`footer` for `view_<alias>` / `<alias>.view`.
3. If a token view string was found but does not exist and has no `::`, retry it prefixed with `baseView`; if it still does not exist, delegate to `parentTheme->resolveView($alias)`.
4. **`baseView + '.' + alias`** fallback — `baseView.<alias>` (dots) or the dashed variant, if `view()->exists()`.
5. Delegate to `parentTheme->resolveView($alias)` (ultimately Tailwind).
6. Final fallback: `livewire-powergrid::components.structure.<alias>`.

Consequences worth remembering:

- **Prefer tokens over new blades.** When a token names a view the theme does not ship, step 3/5 inherits the parent's blade. DaisyUI ships **zero** blades and renders entirely through Tailwind's markup + its own tokens — imitate this. `resources/views/components/themes/daisyui/` no longer exists.
- Feature views (export, toggle-columns, soft-deletes, summarize, detail, tabs, etc.) need **no** view declaration — they resolve by `baseView + alias`, then the parent chain, then `components.structure.*`.
- Only declare `->view()` on a section/sub-component when your theme actually ships that Blade file (as Flux does for its `<flux:*>` components). Omit it to inherit the parent's view.
- A view value containing `::` is used verbatim; a bare value is treated as an alias and prefixed with `baseView` by `HasProperties::toArray()`.
