# v6 -> v7 Theme Migration Reference

This is the canonical, complete reference for the `upgrade-theme-v6-to-v7` skill: how v7 tokens are produced, the v7 token surface, every v6 -> v7 mapping, the builder-class method lists, and a full worked example. Read [`SKILL.md`](SKILL.md) first for the workflow.

## How v7 produces tokens

- **`struct()` is tiny.** It returns a `Components\ThemeBuilder` that only sets `baseView` (or, for `ArrayTheme`, a plain array). The token tree is **not** in `struct()`.
- **Token groups are per-section methods.** Each returns `['<group>' => [...]]`. The list comes from `Theme::themeTokenMethods()`: `layout`, `header`, `table`, `footer`, `cols`, `tabs`, `filter`, `editable`, `toggleable`. Write each as a plain nested array (closest to v6) or with the `$this->section('<group>', fn (...) => ...)` helper.
- **`section()` / `toArray()` snake_case every key.** So `->thActions('...')` (or the array key) becomes `table.layout.th_actions`, `->subContainer('...')` becomes `header.layout.sub_container`, `->tabActive('...')` becomes `tabs.tab_active`. Write camelCase in the fluent form; the resolved token path is snake_case.
- **`baseView`** is prefixed to any `view*` property that does not already contain `::`. So `->view('header')` under a `bootstrap5` base resolves to `livewire-powergrid::components.themes.bootstrap5.header`.
- **`parentTheme` fills the rest.** With `protected ?string $parentTheme = Tailwind::class;`, `Theme::resolveTokens()` merges the child over the parent with `array_replace_recursive` — declare only what differs. A standalone theme (like `Tailwind`) leaves `$parentTheme = null` and defines every section itself.
- **Merge order in `resolveTokens()`:** `struct()` → parent theme → the section methods (`themeTokenMethods()`) → plugin `themeTokens()` → `config('livewire-powergrid.theme_overrides')` (last, highest precedence).
- There is **no `$name` property**. `Theme::name()` derives the theme name from the class basename (kebab-cased). Do not add `public string $name`.
- **`ArrayTheme` alternative.** Instead of a builder theme you can extend `ArrayTheme` and override `struct(): array`, or build one ad-hoc with `ArrayTheme::fromArray($tokens, parentTheme: Tailwind::class, name: '...')` / `ArrayTheme::fromFile($path, ...)`.

## v7 token surface (from `src/Themes/Tailwind.php`)

`Tailwind.php` is the source of truth — verify against it rather than trusting this list if the source has changed. Leaves are shown snake_cased (the resolved token path).

| Section method | Leaf tokens (snake_case) |
|:---------------|:-------------------------|
| (root, from `struct()`) | `name`, `base_view` |
| `layout()` | `wrapper`, `card`, `outside_filters` |
| `header()` | `view` |
| `header()` → `layout` | `container`, `sub_container`, `actions_container`, `actions` |
| `header()` → `search_box` | `view`, `container`, `relative_main`, `input`, `icon_search_wrapper`, `icon_close_wrapper`, `icon_close`, `icon_search`, `icon`, `icon_clear` |
| `header()` → `toggle_columns` | `button`, `icon_class`, `label`, `menu`, `menu_item`, `panel` |
| `header()` → `soft_deletes` | `button`, `icon_class`, `label`, `menu`, `menu_item` |
| `header()` → `filters` | `wrapper`, `button`, `icon_class`, `label` |
| `header()` → `filter_builder` | `button`, `icon_class`, `label`, `badge` |
| `header()` → `export` | `button`, `icon_class`, `label`, `menu`, `menu_item` |
| `header()` → `enabled_filters` | `wrapper`, `label`, `icon_class`, `pill`, `pill_clear_all` |
| `table()` → `layout` | `container`, `table`, `thead`, `tr`, `thead_tr`, `empty_state`, `th`, `th_actions`, `tbody`, `td`, `td_actions` (DaisyUI adds `tr_striped`, `tr_not_striped`) |
| `table()` → `body.tr` | `responsive`, `responsive_toggle_icon` |
| `table()` → `checkbox` | `th`, `base`, `label`, `input` |
| `table()` → `radio` | `th`, `base`, `label`, `input` |
| `cols()` | `div` |
| `footer()` | `view` |
| `footer()` → `layout` | `container`, `select` |
| `footer()` → `pagination` | `view` |
| `tabs()` | `view` (optional), `list`, `tab`, `tab_active`, `tab_inactive`, `badge`, `badge_active`, `badge_inactive` |

Merged in from the remaining section methods:

| Method | Leaf tokens |
|:-------|:------------|
| `editable()` | `editable.clickable`, `editable.input`, `editable.error` |
| `toggleable()` | `toggleable.color_on`, `toggleable.color_off`, `toggleable.color_on_dark`, `toggleable.color_off_dark`, `toggleable.knob_on` |
| `filter()` | `filter.label`; `filter.boolean.{view,base,select}`; `filter.date_picker.{view,base,input}`; `filter.multi_select.{base,select}`; `filter.number.{view,base,input}`; `filter.select.{view,base,select}`; `filter.input_text.{view,base,select,input}`; `filter.input`; `filter.dropdown.*`; `filter.flyout.*` |

`filter.dropdown.*` = `view, wrapper, trigger, badge, panel, header, title, body, grid, footer, reset, clear, apply` (the popover for `config('...filter')` = `dropdown`).
`filter.flyout.*` = `view, overlay, panel, panel_left, panel_right, header, title, close, body, footer, clear_all` (the drawer for `config('...filter')` = `flyout`).

## Builder classes and their methods

When building the section methods you use these typed builders (all in `src/Themes/Components/`):

| Class | Methods (each returns `self`) |
|:------|:------------------------------|
| `ThemeBuilder` | `make(string $name)`, `baseView()`, `layout()`, `header()`, `table()`, `footer()`, `cols()`, `tabs()` |
| `Layout` | `wrapper()`, `card()`, `outsideFilters()`, `container()`, `subContainer()`, `actionsContainer()`, `actions()`, `table()`, `thead()`, `tbody()`, `tr()`, `theadTr()`, `emptyState()`, `trStriped()`, `trNotStriped()`, `th()`, `thActions()`, `td()`, `tdActions()`, `select()` |
| `Header` | `view()`, `layout(Closure\|array)`, `searchBox(Closure\|array)`, `toggleColumns(Closure)`, `softDeletes(Closure)`, `filters(Closure)`, `filterBuilder(Closure)`, `export(Closure)`, `enabledFilters(Closure)` |
| `HeaderButton` | `button()`, `icon()`, `iconClass()`, `label()`, `menu()`, `menuItem()`, `panel()`, `badge()`, `wrapper()`, `pill()`, `pillClearAll()`, `view()` |
| `SearchBox` | `view()`, `container()`, `relativeMain()`, `input()`, `iconSearchWrapper()`, `iconCloseWrapper()`, `iconClose()`, `iconSearch()`, `icon()`, `iconClear()` |
| `Table` | `view()`, `layout()`, `body()`, `checkbox()`, `radio()` |
| `Body` | `tr(Closure\|string)` |
| `Tr` | `base()`, `responsive()`, `responsiveToggleIcon()` |
| `Checkbox` / `Radio` | `th()`, `base()`, `label()`, `input()` |
| `Cols` | `div()` |
| `Tabs` | `view()`, `list()`, `tab()`, `tabActive()`, `tabInactive()`, `badge()`, `badgeActive()`, `badgeInactive()` |
| `Footer` | `view()`, `layout(Closure\|array)`, `pagination(Closure\|array\|string)` |
| `Pagination` | `__construct(string $view = '')`, `view()` — a bare string like `->pagination('pagination')` sets `footer.pagination.view` |
| `Filter` | `label()`, `input()`, `boolean(Closure)`, `datePicker(Closure)`, `multiSelect(Closure)`, `number(Closure)`, `select(Closure)`, `inputText(Closure)`, `dropdown(Closure)`, `flyout(Closure)` |
| `Dropdown` | `view()`, `wrapper()`, `trigger()`, `badge()`, `panel()`, `header()`, `title()`, `body()`, `grid()`, `footer()`, `reset()`, `clear()`, `apply()` |
| `Flyout` | `view()`, `overlay()`, `panel()`, `panelLeft()`, `panelRight()`, `header()`, `title()`, `close()`, `body()`, `footer()`, `clearAll()` |
| `Component` | `view()`, `base()`, `input()`, `select()`, `th()`, `label()`, `clickable()`, `error()`, plus `fill(array)` — used for `editable`, `toggleable`, and array-form filter types |

`Layout::trStriped()` and `trNotStriped()` are optional (not in the Tailwind base; DaisyUI uses them). The `tabs` group and the header-control buttons (`toggleColumns`/`softDeletes`/`filters`/`filterBuilder`/`export`/`enabledFilters`) are **new in v7** — they have no v6 equivalent, so inherit them from Tailwind unless your framework needs different classes.

## Complete v6 -> v7 mapping table

The **section / builder chain** column is what you write; the **v7 token path** column is the resolved snake_case key (what the Validation diff compares).

### Layout (NEW in v7)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| N/A | `layout()` → `wrapper()` | `layout.wrapper` | Outer wrapper around the whole grid |
| N/A | `layout()` → `card()` | `layout.card` | Card/border wrapper (NEW) |
| N/A | `layout()` → `outsideFilters()` | `layout.outside_filters` | Container for filters rendered outside the table |

### Header (NEW in v7)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| N/A | `header()` → `view()` | `header.view` | Usually `'header'` |
| N/A | `header()` → `layout()` → `container()` | `header.layout.container` | |
| N/A | `header()` → `layout()` → `subContainer()` | `header.layout.sub_container` | |
| N/A | `header()` → `layout()` → `actionsContainer()` | `header.layout.actions_container` | |
| `table()['layout']['actions']` | `header()` → `layout()` → `actions()` | `header.layout.actions` | Moved out of `table` in v6 |

The header-control buttons (`toggleColumns`, `softDeletes`, `filters`, `filterBuilder`, `export`, `enabledFilters`) are new in v7 and have no v6 equivalent — inherit from Tailwind.

### SearchBox (moved under header)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| N/A | `header()` → `searchBox()` → `view()` | `header.search_box.view` | Usually `'header.search'` |
| `searchBox()['input']` | `header()` → `searchBox()` → `input()` | `header.search_box.input` | |
| `searchBox()['iconClose']` | `header()` → `searchBox()` → `iconClose()` | `header.search_box.icon_close` | |
| `searchBox()['iconSearch']` | `header()` → `searchBox()` → `iconSearch()` | `header.search_box.icon_search` | |
| N/A | `header()` → `searchBox()` → `container()`/`relativeMain()`/`iconSearchWrapper()`/`iconCloseWrapper()`/`icon()`/`iconClear()` | `header.search_box.*` | Wrappers + icon view aliases |

### Table layout (flattened)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| `table()['layout']['container']` | `table()` → `layout()` → `container()` | `table.layout.container` | |
| `table()['layout']['table']` | `table()` → `layout()` → `table()` | `table.layout.table` | |
| `table()['header']['thead']` | `table()` → `layout()` → `thead()` | `table.layout.thead` | |
| `table()['header']['tr']` | `table()` → `layout()` → `tr()` | `table.layout.tr` | |
| `table()['header']['th']` | `table()` → `layout()` → `th()` | `table.layout.th` | |
| `table()['header']['thAction']` | `table()` → `layout()` → `thActions()` | `table.layout.th_actions` | Renamed |
| `table()['body']['tbody']` | `table()` → `layout()` → `tbody()` | `table.layout.tbody` | |
| `table()['body']['td']` | `table()` → `layout()` → `td()` | `table.layout.td` | |
| `table()['body']['tdActionsContainer']` | `table()` → `layout()` → `tdActions()` | `table.layout.td_actions` | Renamed |
| N/A | `table()` → `layout()` → `theadTr()`/`emptyState()` | `table.layout.thead_tr` / `table.layout.empty_state` | NEW in v7 |

### Table body responsive (NEW in v7)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| N/A | `table()` → `body()` → `tr()` → `responsive()` | `table.body.tr.responsive` | `body()->tr()` takes a `Tr` closure or a plain string |
| N/A | `table()` → `body()` → `tr()` → `responsiveToggleIcon()` | `table.body.tr.responsive_toggle_icon` | |

### Table checkbox / radio (moved under table)

| V6 source | Builder chain | V7 token path |
|:----------|:--------------|:--------------|
| `checkbox()['th'\|'base'\|'label'\|'input']` | `table()` → `checkbox()` → `th()`/`base()`/`label()`/`input()` | `table.checkbox.*` |
| `radio()['th'\|'base'\|'label'\|'input']` | `table()` → `radio()` → `th()`/`base()`/`label()`/`input()` | `table.radio.*` |

### Cols

| V6 source | Builder chain | V7 token path |
|:----------|:--------------|:--------------|
| `cols()['div']` | `cols()` → `div()` | `cols.div` |

### Footer (nested layout)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| `footer()['view']` | `footer()` → `view()` | `footer.view` | Pass the alias only (e.g. `'footer'`), not `$this->root().'.footer'` |
| `footer()['footer']` | `footer()` → `layout()` → `container()` | `footer.layout.container` | Renamed and nested |
| `footer()['select']` | `footer()` → `layout()` → `select()` | `footer.layout.select` | Nested under `layout` |
| `footer()['footer_with_pagination']` | `footer()` → `pagination('...')` | `footer.pagination.view` | A bare string sets the pagination view |

### Tabs (NEW in v7 — no v6 equivalent)

`tabs()` → `list`/`tab`/`tabActive`/`tabInactive`/`badge`/`badgeActive`/`badgeInactive` (+ optional `view`) → `tabs.*`. Inherit from Tailwind unless you want different tab styling.

### Editable (separate `editable()` method)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| `editable()['view']` | — | — | Discard. Plugin blade is `powergrid-plugins::Editable.index` |
| N/A | `Component->clickable()` | `editable.clickable` | NEW in v7 |
| `editable()['input']` | `Component->input()` | `editable.input` | |
| N/A | `Component->error()` | `editable.error` | NEW in v7 |

### Toggleable (separate `toggleable()` method — five color tokens)

v7 `toggleable` is a pure CSS switch built from five color variables set with `->fill([...])` on a `Component`. The v6 `view`, `base`, `label`, `input`, and `role` keys are all removed.

| Builder (`->fill([...])` key) | V7 token path | Notes |
|:------------------------------|:--------------|:------|
| `colorOn` | `toggleable.color_on` | "on" track color |
| `colorOff` | `toggleable.color_off` | "off" track color |
| `colorOnDark` | `toggleable.color_on_dark` | "on" color (dark mode) |
| `colorOffDark` | `toggleable.color_off_dark` | "off" color (dark mode) |
| `knobOn` | `toggleable.knob_on` | knob color when on |

v6 `toggleable()['view'\|'base'\|'label'\|'input'\|'role']` are all **removed** — discard them.

### Filters (separate `filter()` method)

Build with the `Components\Filter` builder. Each filter type is a `Component` closure.

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| N/A | `->label()` | `filter.label` | NEW in v7 |
| `filterBoolean()['view'\|'base'\|'select']` | `->boolean(fn ($c) => ...)` | `filter.boolean.{view,base,select}` | |
| `filterDatePicker()['view'\|'base'\|'input']` | `->datePicker(fn ($c) => ...)` | `filter.date_picker.{view,base,input}` | Base points at `powergrid-plugins::Flatpickr.index` |
| `filterMultiSelect()['view'\|'base'\|'select']` | `->multiSelect(fn ($c) => ...)` | `filter.multi_select.{base,select}` | Markup is `<x-livewire-powergrid::inputs.select>` |
| `filterNumber()['view'\|'input']` | `->number(fn ($c) => ...)` | `filter.number.{view,base,input}` | `base` present in v7 |
| `filterSelect()['view'\|'base'\|'select']` | `->select(fn ($c) => ...)` | `filter.select.{view,base,select}` | |
| `filterInputText()['view'\|'base'\|'select'\|'input']` | `->inputText(fn ($c) => ...)` | `filter.input_text.{view,base,select,input}` | |
| N/A | `->input()` | `filter.input` | NEW - global input styling |
| N/A | `->dropdown(fn (Components\Dropdown $d) => ...)` | `filter.dropdown.*` | NEW - dropdown popover |
| N/A | `->flyout(fn (Components\Flyout $f) => ...)` | `filter.flyout.*` | NEW - filter drawer |

### v6 keys removed in v7

These v6 keys have no v7 equivalent. Discard them:

| V6 key | Reason |
|:-------|:-------|
| `table()['layout']['base']` | Removed |
| `table()['layout']['div']` | Removed |
| `table()['body']['tbodyEmpty']` | Removed - simplified |
| `table()['body']['tdEmpty']` | Removed - simplified |
| `table()['body']['tdSummarize']` | Removed - simplified |
| `table()['body']['trSummarize']` | Removed - simplified |
| `table()['body']['tdFilters']` | Removed - simplified |
| `table()['body']['trFilters']` | Removed - simplified |

## Full worked example

### v6 Bootstrap5 (legacy)

```php
<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Bootstrap5 extends Theme
{
    public function table(): array
    {
        return [
            'layout' => [
                'table'     => 'table table-hover table-striped',
                'container' => 'my-0',
                'div'       => '',   // removed in v7
                'base'      => 'card', // removed in v7
            ],
            'header' => [
                'thead'     => '',
                'tr'        => '',
                'th'        => 'fw-bold text-secondary',
                'thAction'  => 'text-center',
            ],
            'body' => [
                'tbody' => '',
                'tr'    => '',
                'td'    => 'align-middle text-nowrap',
            ],
        ];
    }

    public function checkbox(): array
    {
        return [
            'th'    => 'text-center',
            'base'  => 'form-check',
            'label' => 'form-check-label',
            'input' => 'form-check-input',
        ];
    }

    public function searchBox(): array
    {
        return [
            'input'      => 'form-control',
            'iconSearch' => 'bi bi-search',
            'iconClose'  => 'bi bi-x',
        ];
    }

    public function footer(): array
    {
        return [
            'view'                   => $this->root().'.footer',
            'select'                 => 'form-select',
            'footer'                 => 'd-flex justify-content-between',
            'footer_with_pagination' => 'pagination',
        ];
    }
}
```

### v7 Bootstrap5 (migrated)

```php
<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components;

class Bootstrap5 extends Theme
{
    protected ?string $parentTheme = Tailwind::class;

    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())->baseView($this->baseView());
    }

    protected function baseView(): string
    {
        return 'livewire-powergrid::components.themes.bootstrap5';
    }

    /** @return array<string, mixed> */
    public function layout(): array
    {
        return $this->section('layout', fn (Components\Layout $layout) => $layout
            ->wrapper('')
            ->card('card')
            ->outsideFilters('')
        );
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
                ->actions('btn btn-outline-secondary btn-sm')
            )
            ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                ->view('header.search')
                ->container('d-flex justify-content-end')
                ->relativeMain('position-relative')
                ->input('form-control')
                ->iconSearchWrapper('position-absolute top-50 start-0 translate-middle-y ps-2')
                ->iconCloseWrapper('position-absolute top-50 end-0 translate-middle-y pe-2')
                ->iconClose('bi bi-x')
                ->iconSearch('bi bi-search')
            )
        );
    }

    /** @return array<string, mixed> */
    public function table(): array
    {
        return $this->section('table', fn (Components\Table $table) => $table
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('my-0')
                ->table('table table-hover table-striped')
                ->thead('')
                ->tr('')
                ->th('fw-bold text-secondary')
                ->thActions('text-center')
                ->tbody('')
                ->td('align-middle text-nowrap')
                ->tdActions('text-end')
            )
            ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox
                ->th('text-center')
                ->base('form-check')
                ->label('form-check-label')
                ->input('form-check-input')
            )
            ->radio(fn (Components\Radio $radio) => $radio
                ->th('text-center')
                ->base('form-check')
                ->label('form-check-label')
                ->input('form-check-input')
            )
        );
    }

    /** @return array<string, mixed> */
    public function cols(): array
    {
        return ['cols' => ['div' => 'd-flex align-items-center gap-1']];
    }

    /** @return array<string, mixed> */
    public function footer(): array
    {
        return $this->section('footer', fn (Components\Footer $footer) => $footer
            ->view('footer')
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('d-flex justify-content-between')
                ->select('form-select')
            )
            ->pagination('pagination')
        );
    }

    /** @return array<string, mixed> */
    public function editable(): array
    {
        return [
            'editable' => (new Components\Component())
                ->view('livewire-powergrid::components.themes.bootstrap5.editable')
                ->clickable('py-2')
                ->input('form-control')
                ->error('invalid-feedback d-block')
                ->toArray(),
        ];
    }

    /** @return array<string, mixed> */
    public function toggleable(): array
    {
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
        return [
            'filter' => (new Components\Filter())
                ->label('form-label')
                ->boolean(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.boolean')
                    ->select('form-select')
                )
                ->datePicker(fn (Components\Component $c) => $c
                    ->view('powergrid-plugins::Flatpickr.index')
                    ->input('form-control')
                )
                ->multiSelect(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.multi-select')
                    ->select('form-select')
                )
                ->number(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.number')
                    ->input('form-control')
                )
                ->select(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.select')
                    ->select('form-select')
                )
                ->inputText(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.input-text')
                    ->select('form-select')
                    ->input('form-control')
                )
                ->input('form-control')
                ->toArray(),
        ];
    }
}
```

Any of these array-returning methods can be written as a plain nested array
instead of `section()` — often the smallest diff from the v6 source. `cols()`
above shows the plain-array form.

## Notes on inheritance

With `protected ?string $parentTheme = Tailwind::class;`, `Theme::resolveTokens()` merges the child's tokens over the parent's with `array_replace_recursive`, so a child only needs to declare the sections it overrides. A standalone theme (like the base `Tailwind`) leaves `$parentTheme = null` and defines every section itself.

A subclass can also **append** a few tokens to an inherited section by wrapping its `section()` result with `array_replace_recursive`, as DaisyUI does for `header.toggle_columns.item_label` and its pagination classes, and Flux does for `table.body.td.actions_wrapper` and its pagination classes. You do **not** need to override `resolveTokens()` for this.

Finally: **prefer tokens over new blades.** DaisyUI ships zero Blade files and inherits Tailwind's markup through `parentTheme` + the view-resolution fallback in `Theme::doResolveView()`. Add a Blade file (and a `->view()` token) only for markup that is genuinely different (as Flux does for its `<flux:*>` components).
