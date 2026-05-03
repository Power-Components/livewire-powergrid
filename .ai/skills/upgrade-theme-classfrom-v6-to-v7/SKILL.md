---
compatibility: opencode
description: >
    Migrates a v6 Theme class to the v7 unified struct() architecture
metadata:
    audience: laravel-developers
    framework: laravel-12
    package: livewire-powergrid
    agent: developer
name: upgrade-theme-classfrom-v6-to-v7
---

## What I do

- Read a legacy v6 theme class (like Bootstrap, DaisyUI) from GitHub or local disk.
- Create a new v7 theme class (e.g., BootstrapV7.php) implementing the unified `struct()` pattern.
- Map all legacy CSS configurations from multiple methods (`table()`, `header()`, `filterBoolean()`, etc.) into the new `struct()` array map.
- Validate that the generated v7 class matches the exact token structure of the base Tailwind v7 theme.
- Replace legacy usages in the codebase to point to the new `[ThemeName]V7` class.

## When to use me

Use me when:
- Porting legacy PowerGrid v6 themes to the new v7 architecture.
- Upgrading third-party custom themes that still use legacy array methods instead of the unified `struct()`.

## How to use me

```
Use the 'upgrade-theme-classfrom-v6-to-v7' skill to migrate the v6 Bootstrap theme located at https://raw.githubusercontent.com/Power-Components/livewire-powergrid/6.x/src/Themes/Bootstrap5.php
```

## Workflow

When invoked, execute the following steps precisely:

### 1. Analyze the v7 Base Structure
- Read the current `src/Themes/Tailwind.php` (v7) in the project.
- Extract all keys from the `struct()` method. This is the **Source of Truth** for the required structure. All themes must have these exact keys.

### 2. Fetch the v6 Legacy Theme
- Access the provided v6 theme file via URL or local path.
- Analyze how classes were returned in v6 (e.g., `table()`, `header()`, `footer()`, `checkbox()`, `filterBoolean()`, etc.).

### 3. Create the New Theme Class (v7)
- Create a new file in `src/Themes/` named `[ThemeName]V7.php` (e.g., `BootstrapV7.php`).
- Ensure it extends `\PowerComponents\LivewirePowerGrid\Themes\Theme`.
- Create the unified `public function struct(): array` method.
- Map the legacy CSS classes from the v6 methods into the corresponding v7 `struct()` keys using the mapping table below.
- Discard logic like `@if` or theme conditionals.
- Ensure the `views()` method maps to the correct v7 micro-views (or leave empty to inherit Tailwind views).

### V6 to V7 Key Mapping
Use this exact mapping to translate v6 methods into v7 `struct()` keys. Notice that v6 returned arrays, so map the corresponding keys:

| Legacy v6 Method | v7 struct() Key |
| :--- | :--- |
| `table()['layout']['base']` | `table.layout.base` |
| `table()['layout']['div']` | `table.layout.div` |
| `table()['layout']['table']` | `table.base` |
| `table()['layout']['container']` | `table.layout.container` |
| `table()['layout']['actions']` | `table.layout.actions` |
| `table()['header']['thead']` | `table.header.thead` |
| `table()['header']['tr']` | `table.header.tr` |
| `table()['header']['th']` | `table.header.th` |
| `table()['header']['thAction']` | `table.header.th_action` |
| `table()['body']['tbody']` | `table.body.wrapper` |
| `table()['body']['tbodyEmpty']` | `table.body.empty_state` |
| `table()['body']['tr']` | `table.body.tr.wrapper` |
| `table()['body']['td']` | `table.body.td.wrapper` |
| `table()['body']['tdEmpty']` | `table.body.td.empty_state` |
| `table()['body']['tdSummarize']` | `table.body.td.summarize.wrapper` |
| `table()['body']['trSummarize']` | `table.body.tr.summarize` |
| `table()['body']['tdFilters']` | `table.body.td.filters` |
| `table()['body']['trFilters']` | `table.body.tr.filters` |
| `table()['body']['tdActionsContainer']` | `table.body.td.actions_wrapper` |
| `footer()['view']` | `footer.view` |
| `footer()['select']` | `footer.select` |
| `footer()['footer']` | `footer.footer` |
| `footer()['footer_with_pagination']` | `footer.footer_with_pagination` |
| `cols()['div']` | `cols.div` |
| `editable()['view']` | `editable.view` |
| `editable()['input']` | `editable.input` |
| `toggleable()['view']` | `toggleable.view` |
| `checkbox()['th']` | `checkbox.th` |
| `checkbox()['base']` | `checkbox.base` |
| `checkbox()['label']` | `checkbox.label` |
| `checkbox()['input']` | `checkbox.input` |
| `radio()['th']` | `radio.th` |
| `radio()['base']` | `radio.base` |
| `radio()['label']` | `radio.label` |
| `radio()['input']` | `radio.input` |
| `filterBoolean()['view']` | `filter.boolean.view` |
| `filterBoolean()['base']` | `filter.boolean.base` |
| `filterBoolean()['select']` | `filter.boolean.select` |
| `filterDatePicker()['view']` | `filter.date_picker.view` |
| `filterDatePicker()['base']` | `filter.date_picker.base` |
| `filterDatePicker()['input']` | `filter.date_picker.input` |
| `filterMultiSelect()['view']` | `filter.multi_select.view` |
| `filterMultiSelect()['base']` | `filter.multi_select.base` |
| `filterMultiSelect()['select']` | `filter.multi_select.select` |
| `filterNumber()['view']` | `filter.number.view` |
| `filterNumber()['input']` | `filter.number.input` |
| `filterSelect()['view']` | `filter.select.view` |
| `filterSelect()['base']` | `filter.select.base` |
| `filterSelect()['select']` | `filter.select.select` |
| `filterInputText()['view']` | `filter.input_text.view` |
| `filterInputText()['base']` | `filter.input_text.base` |
| `filterInputText()['select']` | `filter.input_text.select` |
| `filterInputText()['input']` | `filter.input_text.input` |
| `searchBox()['input']` | `search_box.input` |
| `searchBox()['iconClose']` | `search_box.icon_close` |
| `searchBox()['iconSearch']` | `search_box.icon_search` |

### 4. Validation (Key Syncing)
- Run a comparison script (in PHP) between `Tailwind.php` (v7) and `[ThemeName]V7.php`.
- Ensure **every single key** present in Tailwind v7 is present in the new v7 theme.
- Fill missing keys with empty strings `''` or standard values.

### 5. Update Usages (Refactoring)
- Search the codebase (using `grep` or `glob`) for references to the old theme class.
- Update these references to point to the newly created `[ThemeName]V7::class`.
