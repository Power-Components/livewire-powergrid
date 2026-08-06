# v6 -> v7 Theme Migration Reference

This is the canonical, complete reference for the `upgrade-theme-v6-to-v7` skill: the base v7 token map, every v6 -> v7 mapping, the builder-class method lists, and a full worked example. Read [`SKILL.md`](SKILL.md) first for the workflow.

## How the builder produces tokens

- `struct()` returns a `Components\ThemeBuilder`. Each section method takes a **typed closure** and stores the sub-builder's `toArray()` under a key.
- `toArray()` (in `Components/HasProperties.php`) **snake_cases every key**. So the builder call `->thActions('...')` becomes the token `table.layout.th_actions`, `->subContainer('...')` becomes `header.layout.sub_container`, and so on. Write camelCase; the resolved token path is snake_case.
- `baseView(...)` is prefixed to any property whose key starts with `view` and that does not already contain `::`. That is why `->view('header')` under a `bootstrap5` base resolves to `livewire-powergrid::components.themes.bootstrap5.header`.
- `editable()`, `toggleable()`, and `filter()` are **not** part of `struct()`. `Theme::resolveTokens()` merges them in via `themeTokenMethods()` (`['filter', 'editable', 'toggleable']`), plus any plugin-contributed tokens.
- There is **no `$name` property**. `Theme::name()` derives the theme name from the class basename (kebab-cased). Do not add `public string $name`.

## Base v7 token map (from `src/Themes/Tailwind.php`)

The base `Tailwind` `struct()` defines **41** leaf tokens. Verify against the file rather than trusting this count if the source has changed.

| Section | Leaf tokens (snake_case) |
|:--------|:-------------------------|
| (root) | `name`, `base_view` |
| `layout` | `wrapper`, `outside_filters` |
| `header` | `view` |
| `header.layout` | `container`, `sub_container`, `actions_container`, `actions` |
| `header.search_box` | `view`, `container`, `relative_main`, `input`, `icon_search_wrapper`, `icon_close_wrapper`, `icon_close`, `icon_search` |
| `table.layout` | `container`, `table`, `thead`, `tr`, `th`, `th_actions`, `tbody`, `td`, `td_actions` |
| `table.body.tr` | `responsive`, `responsive_toggle_icon` |
| `table.checkbox` | `th`, `base`, `label`, `input` |
| `table.radio` | `th`, `base`, `label`, `input` |
| `cols` | `div` |
| `footer` | `view` |
| `footer.layout` | `container`, `select` |
| `footer.pagination` | `view` |

Merged in from separate methods:

| Method | Leaf tokens |
|:-------|:------------|
| `editable()` (4) | `editable.view`, `editable.clickable`, `editable.input`, `editable.error` |
| `toggleable()` (5) | `toggleable.color_on`, `toggleable.color_off`, `toggleable.color_on_dark`, `toggleable.color_off_dark`, `toggleable.knob_on` |
| `filter()` (21) | `filter.label`; `filter.boolean.{view,base,select}`; `filter.date_picker.{view,base,input}`; `filter.multi_select.{view,base,select}`; `filter.number.{view,base,input}`; `filter.select.{view,base,select}`; `filter.input_text.{view,base,select,input}`; `filter.input` |

## Builder classes and their methods

When building `struct()` you use these typed builders (all in `src/Themes/Components/`):

| Class | Methods (each returns `self`) |
|:------|:------------------------------|
| `ThemeBuilder` | `make(string $name)`, `baseView()`, `layout()`, `header()`, `table()`, `footer()`, `cols()` |
| `Layout` | `wrapper()`, `outsideFilters()`, `container()`, `subContainer()`, `actionsContainer()`, `actions()`, `table()`, `thead()`, `tbody()`, `tr()`, `trStriped()`, `trNotStriped()`, `th()`, `thActions()`, `td()`, `tdActions()`, `tfoot()`, `select()` |
| `Header` | `view()`, `layout(Closure\|array)`, `searchBox(Closure\|array)` |
| `SearchBox` | `view()`, `container()`, `relativeMain()`, `input()`, `iconSearchWrapper()`, `iconCloseWrapper()`, `iconClose()`, `iconSearch()` |
| `Table` | `layout()`, `body()`, `checkbox()`, `radio()`, plus view aliases (`view()`, `viewLayout()`, `viewHeader()`, `viewRow()`, `viewCols()`, `viewThEmpty()`, `viewInlineFilters()`, `viewCheckboxAll()`, `viewCheckboxRow()`, `viewRadioRow()`) |
| `Body` | `tr(Closure\|string)`, `td(Closure\|string)` |
| `Tr` | `base()`, `responsive()`, `responsiveToggleIcon()` |
| `Td` | `base()`, `actionsWrapper()` |
| `Checkbox` / `Radio` | `th()`, `base()`, `label()`, `input()` |
| `Cols` | `div()` |
| `Footer` | `view()`, `layout(Closure\|array)`, `pagination(Closure\|array\|string)` |
| `Pagination` | constructor `__construct(string $view = '')`, `make()`, `view()` — a bare string like `->pagination('pagination')` sets `footer.pagination.view` |
| `Component` | `view()`, `base()`, `input()`, `select()`, `th()`, `label()`, `clickable()`, `error()`, `container()`, `relativeMain()`, `iconSearchWrapper()`, `iconCloseWrapper()`, `iconClose()`, `iconSearch()`, plus `fill(array)` — used for `editable`, `toggleable`, and each `filter` type |
| `Filter` | `label()`, `input()`, `boolean(Closure)`, `datePicker(Closure)`, `multiSelect(Closure)`, `number(Closure)`, `select(Closure)`, `inputText(Closure)` |

`Layout::trStriped()`, `trNotStriped()`, and `tfoot()` are optional tokens (not in the Tailwind base; DaisyUI uses `trStriped`/`trNotStriped`). Add them only when the framework needs them.

## Complete v6 -> v7 mapping table

The **builder chain** column is what you write; the **v7 token path** column is the resolved snake_case key (what the Validation diff compares).

### Layout (NEW in v7)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| N/A | `->layout()->wrapper()` | `layout.wrapper` | Outer wrapper around the whole grid |
| N/A | `->layout()->outsideFilters()` | `layout.outside_filters` | Container for filters rendered outside the table |

### Header (NEW in v7)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| N/A | `->header()->view()` | `header.view` | Usually `'header'` |
| N/A | `->header()->layout()->container()` | `header.layout.container` | |
| N/A | `->header()->layout()->subContainer()` | `header.layout.sub_container` | |
| N/A | `->header()->layout()->actionsContainer()` | `header.layout.actions_container` | |
| `table()['layout']['actions']` | `->header()->layout()->actions()` | `header.layout.actions` | Moved out of `table` in v6 |

### SearchBox (moved under header)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| N/A | `->header()->searchBox()->view()` | `header.search_box.view` | Usually `'header.search'` |
| N/A | `->header()->searchBox()->container()` | `header.search_box.container` | |
| N/A | `->header()->searchBox()->relativeMain()` | `header.search_box.relative_main` | |
| `searchBox()['input']` | `->header()->searchBox()->input()` | `header.search_box.input` | |
| N/A | `->header()->searchBox()->iconSearchWrapper()` | `header.search_box.icon_search_wrapper` | |
| N/A | `->header()->searchBox()->iconCloseWrapper()` | `header.search_box.icon_close_wrapper` | |
| `searchBox()['iconClose']` | `->header()->searchBox()->iconClose()` | `header.search_box.icon_close` | |
| `searchBox()['iconSearch']` | `->header()->searchBox()->iconSearch()` | `header.search_box.icon_search` | |

### Table layout (flattened)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| `table()['layout']['container']` | `->table()->layout()->container()` | `table.layout.container` | |
| `table()['layout']['table']` | `->table()->layout()->table()` | `table.layout.table` | |
| `table()['header']['thead']` | `->table()->layout()->thead()` | `table.layout.thead` | |
| `table()['header']['tr']` | `->table()->layout()->tr()` | `table.layout.tr` | |
| `table()['header']['th']` | `->table()->layout()->th()` | `table.layout.th` | |
| `table()['header']['thAction']` | `->table()->layout()->thActions()` | `table.layout.th_actions` | Renamed |
| `table()['body']['tbody']` | `->table()->layout()->tbody()` | `table.layout.tbody` | |
| `table()['body']['td']` | `->table()->layout()->td()` | `table.layout.td` | |
| `table()['body']['tdActionsContainer']` | `->table()->layout()->tdActions()` | `table.layout.td_actions` | Renamed |

### Table body responsive (NEW in v7)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| N/A | `->table()->body()->tr()->responsive()` | `table.body.tr.responsive` | `body()->tr()` takes a `Tr` closure or a plain string |
| N/A | `->table()->body()->tr()->responsiveToggleIcon()` | `table.body.tr.responsive_toggle_icon` | |

### Table checkbox (moved under table)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| `checkbox()['th']` | `->table()->checkbox()->th()` | `table.checkbox.th` | |
| `checkbox()['base']` | `->table()->checkbox()->base()` | `table.checkbox.base` | |
| `checkbox()['label']` | `->table()->checkbox()->label()` | `table.checkbox.label` | |
| `checkbox()['input']` | `->table()->checkbox()->input()` | `table.checkbox.input` | |

### Table radio (moved under table)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| `radio()['th']` | `->table()->radio()->th()` | `table.radio.th` | |
| `radio()['base']` | `->table()->radio()->base()` | `table.radio.base` | |
| `radio()['label']` | `->table()->radio()->label()` | `table.radio.label` | |
| `radio()['input']` | `->table()->radio()->input()` | `table.radio.input` | |

### Cols

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| `cols()['div']` | `->cols()->div()` | `cols.div` | |

### Footer (nested layout)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| `footer()['view']` | `->footer()->view()` | `footer.view` | Pass the alias only (e.g. `'footer'`), not `$this->root().'.footer'` |
| `footer()['footer']` | `->footer()->layout()->container()` | `footer.layout.container` | Renamed and nested |
| `footer()['select']` | `->footer()->layout()->select()` | `footer.layout.select` | Nested under `layout` |
| `footer()['footer_with_pagination']` | `->footer()->pagination('...')` | `footer.pagination.view` | A bare string sets the pagination view |

### Editable (separate `editable()` method)

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| `editable()['view']` | `Component->view()` | `editable.view` | |
| N/A | `Component->clickable()` | `editable.clickable` | NEW in v7 |
| `editable()['input']` | `Component->input()` | `editable.input` | |
| N/A | `Component->error()` | `editable.error` | NEW in v7 |

### Toggleable (separate `toggleable()` method — five color tokens)

v7 `toggleable` is a pure CSS switch built from five color variables set with `->fill([...])` on a `Component`. The v6 `view`, `base`, `label`, `input`, and `role` keys are all removed.

| V6 source | Builder (`->fill([...])` key) | V7 token path | Notes |
|:----------|:------------------------------|:--------------|:------|
| N/A | `colorOn` | `toggleable.color_on` | NEW - "on" track color |
| N/A | `colorOff` | `toggleable.color_off` | NEW - "off" track color |
| N/A | `colorOnDark` | `toggleable.color_on_dark` | NEW - "on" color (dark mode) |
| N/A | `colorOffDark` | `toggleable.color_off_dark` | NEW - "off" color (dark mode) |
| N/A | `knobOn` | `toggleable.knob_on` | NEW - knob color when on |
| `toggleable()['view']` | REMOVED | — | No longer configurable |
| `toggleable()['base']` | REMOVED | — | No longer configurable |
| `toggleable()['label']` | REMOVED | — | No longer configurable |
| `toggleable()['input']` | REMOVED | — | No longer configurable |
| `toggleable()['role']` | REMOVED | — | No longer configurable |

### Filters (separate `filter()` method)

Build with the `Components\Filter` builder. Each filter type is a `Component` closure.

| V6 source | Builder chain | V7 token path | Notes |
|:----------|:--------------|:--------------|:------|
| N/A | `->label()` | `filter.label` | NEW in v7 |
| `filterBoolean()['view']` | `->boolean(fn ($c) => $c->view())` | `filter.boolean.view` | |
| `filterBoolean()['base']` | `->boolean(fn ($c) => $c->base())` | `filter.boolean.base` | |
| `filterBoolean()['select']` | `->boolean(fn ($c) => $c->select())` | `filter.boolean.select` | |
| `filterDatePicker()['view']` | `->datePicker(fn ($c) => $c->view())` | `filter.date_picker.view` | Tailwind base points at `powergrid-plugins::Flatpickr.index` |
| `filterDatePicker()['base']` | `->datePicker(fn ($c) => $c->base())` | `filter.date_picker.base` | |
| `filterDatePicker()['input']` | `->datePicker(fn ($c) => $c->input())` | `filter.date_picker.input` | |
| `filterMultiSelect()['view']` | `->multiSelect(fn ($c) => $c->view())` | `filter.multi_select.view` | |
| `filterMultiSelect()['base']` | `->multiSelect(fn ($c) => $c->base())` | `filter.multi_select.base` | |
| `filterMultiSelect()['select']` | `->multiSelect(fn ($c) => $c->select())` | `filter.multi_select.select` | |
| `filterNumber()['view']` | `->number(fn ($c) => $c->view())` | `filter.number.view` | |
| N/A | `->number(fn ($c) => $c->base())` | `filter.number.base` | Present in v7 (empty in Tailwind) |
| `filterNumber()['input']` | `->number(fn ($c) => $c->input())` | `filter.number.input` | |
| `filterSelect()['view']` | `->select(fn ($c) => $c->view())` | `filter.select.view` | |
| `filterSelect()['base']` | `->select(fn ($c) => $c->base())` | `filter.select.base` | |
| `filterSelect()['select']` | `->select(fn ($c) => $c->select())` | `filter.select.select` | |
| `filterInputText()['view']` | `->inputText(fn ($c) => $c->view())` | `filter.input_text.view` | |
| `filterInputText()['base']` | `->inputText(fn ($c) => $c->base())` | `filter.input_text.base` | |
| `filterInputText()['select']` | `->inputText(fn ($c) => $c->select())` | `filter.input_text.select` | |
| `filterInputText()['input']` | `->inputText(fn ($c) => $c->input())` | `filter.input_text.input` | |
| N/A | `->input()` | `filter.input` | NEW - global input styling |

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
            )
            ->table(fn (Components\Table $table) => $table
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
                ->body(fn (Components\Body $body) => $body
                    ->tr(fn (Components\Tr $tr) => $tr
                        ->responsive('')
                        ->responsiveToggleIcon('')
                    )
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
            )
            ->cols(fn (Components\Cols $cols) => $cols
                ->div('d-flex align-items-center gap-1')
            )
            ->footer(fn (Components\Footer $footer) => $footer
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
                    ->base('')
                    ->select('form-select')
                )
                ->datePicker(fn (Components\Component $c) => $c
                    ->view('powergrid-plugins::Flatpickr.index')
                    ->base('')
                    ->input('form-control')
                )
                ->multiSelect(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.multi-select')
                    ->base('')
                    ->select('form-select')
                )
                ->number(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.number')
                    ->base('')
                    ->input('form-control')
                )
                ->select(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.select')
                    ->base('')
                    ->select('form-select')
                )
                ->inputText(fn (Components\Component $c) => $c
                    ->view('livewire-powergrid::components.themes.bootstrap5.filters.input-text')
                    ->base('')
                    ->select('form-select')
                    ->input('form-control')
                )
                ->input('form-control')
                ->toArray(),
        ];
    }
}
```

## Notes on inheritance

Themes may set `protected ?string $parentTheme = Tailwind::class;` (as `DaisyUI` and `Flux` do). When a parent theme is set, `Theme::resolveTokens()` merges the child's tokens over the parent's with `array_replace_recursive`, so a child only needs to declare the tokens it overrides. A standalone theme (like the base `Tailwind`) leaves `$parentTheme = null` and must declare every token itself. A theme can also override `resolveTokens()` to append extra token subtrees (as `Flux` does for `pagination.*`).
