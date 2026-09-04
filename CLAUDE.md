# PowerGrid 7.x AI Guidelines

This document outlines the architectural guidelines for AI agents working with **PowerGrid 7.x**.

## 1. Core Principles

The 7.x version prioritizes **simplicity, an adaptable native skeleton, and independent test components**, discarding legacy dependencies.

* **PHP:** 8.3+
* **Livewire:** 4.0+
* **Tailwind CSS:** 4.x+
* **Removed Legacy Dependencies:** Bootstrap, third-party select integrations, Lazy components, and Pulse files.
* **New Required Package:** `power-components/partials` (imported locally).

## 2. Architecture & Theming

* **Theme classes (`src/Themes`):** Tailwind (root), DaisyUI and Flux extend it via `parentTheme`.
* **Section-based authoring:** A theme exposes per-section methods (`layout()`, `header()`, `table()`, `footer()`, `cols()`, `tabs()`, plus `filter()`/`editable()`/`toggleable()`), each returning a token slice; they are auto-merged by `themeTokenMethods()`. `struct()` only sets `baseView()`. Write a slice as a plain nested array (6.x style) or with the fluent builder via the `section()` helper. `ArrayTheme` builds a whole theme from an array.
* **Selection & registry:** `config('livewire-powergrid.theme')` accepts a registered name (`'tailwind'`|`'daisyui'`|`'flux'`) or an FQCN, resolved through `PowerGridManager::$themes` / `registerTheme()`.
* **No-code overrides:** `config('livewire-powergrid.theme_overrides')` merges token overrides at the highest precedence — restyle any token without a Theme class.
* **Absolute Separation:** HTML structure is separated from visual styles; classes live in tokens read by the `theme()` helper.

## 3. Views

* **No Theme Conditionals:** Do not use logic like `@if($theme == 'bootstrap')` in Blade files.
* **Micro-files Strategy:** Table views are single-purpose micro-files under `resources/views/components/structure/` (e.g. `table.blade.php`, `partials/tbody.blade.php`, `table/row.blade.php`).
* **Token-driven, minimal overrides:** A theme ships a blade only when the HTML structure genuinely differs; class-only differences live in tokens. DaisyUI ships **zero** blades (fully token-driven, inheriting Tailwind's views); Flux keeps blades that use `<flux:*>` components.
* **Helpers:** Read tokens with `theme('key')` (or `@theme('key')`) and resolve views with `theme_view('alias')`.

## 4. Server-Side Actions

* **No Cache or JS processing for Actions:** Action logic and buttons are no longer processed via JavaScript or cached.
* **Full Blade Rendering:** Actions are fully rendered as server-side Blade components/fragments via `renderActions()`.

## 5. Performance & Partials

* **Livewire DOM Isolation:** Use the `power-components/partials` fragments to isolate DOM updates and boost performance.
* **Hot Zones:** Demarcated partial zones for isolated updates:
  1. `pg-tbody`
  2. `pg-pagination`
  3. `pg-filter-fields` / `pg-enabled-filters`
  4. `pg-tabs`

## 6. Testing Strategy

* **Self-sufficient Tests:** Tests must be independent and immune to themes.
* **Core Focus:** Focus testing strictly on core engine capabilities: Search, Filters, Order, and Pagination.
* **No Global Fixtures:** Build mini-components at runtime within the test files instead of using global fixtures.

---

## 7. Skills

PowerGrid 7.x provides specialized [Agent Skills](https://agentskills.io/home) for common tasks, distributed via [Laravel Boost](https://laravel.com/docs/13.x/boost). In this repository they live in `resources/boost/skills/`. Consumer apps install them into their AI agents by running `php artisan boost:install` (or `php artisan boost:update --discover`).

### Available Skills

| Skill | Use When |
|-------|----------|
| `powergrid-theme` | Creating a new theme OR updating an existing theme |
| `upgrade-theme-v6-to-v7` | Migrating a v6 Theme class to v7 architecture |
| `create-powergrid-plugin` | Creating a new PowerGrid plugin (Column behavior with Blade/JS/events) |

### Skill Triggers

#### powergrid-theme

Use this skill when the user asks to "create a theme", "add a new theme", "update theme X", "modify theme classes".

Examples:
- "Create a new PrimeReact theme"
- "Update the Tailwind theme to add dark mode support"
- "I need a custom theme for Vuetify"

#### upgrade-theme-v6-to-v7

Use this skill when the user mentions "migrate", "upgrade from v6", "convert v6 theme", "update old theme".

Examples:
- "Migrate my Bootstrap theme from v6 to v7"
- "Upgrade the old Tailwind theme class to the new struct() format"
- "Convert this v6 theme to v7"

#### create-powergrid-plugin

Use this skill when the user asks to "create a plugin", "add a new column behavior", "make a selectable/colorpicker/rating column", "build a plugin".

Examples:
- "Create a selectable plugin for Column::add()->selectable([...])"
- "I need a rating stars plugin for PowerGrid"
- "Build a colorpicker plugin that saves on change"

### Skill Usage Protocol

1. **Recognize the trigger:** Identify if the user's request matches a skill scenario
2. **Load the skill:** Read the corresponding `resources/boost/skills/<skill-name>/SKILL.md` file
3. **Follow the workflow:** Skills contain detailed step-by-step instructions -- follow them precisely
4. **Use provided resources:** Skills may reference additional files (e.g., `REFERENCE.md`) in the same directory
5. **Complete all steps:** Ensure all checklist items in the skill are completed before marking the task done
