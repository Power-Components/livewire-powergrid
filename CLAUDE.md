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

* **Root `/themes` Directory:** This folder is strictly for PHP classes (Tailwind, DaisyUI, Flux).
* **Strict Key Mapping:** All CSS/theme logic is managed through a new unified `struct()` method.
* **Absolute Separation:** HTML structure is completely separated from visual styles.

## 3. Views

* **No Theme Conditionals:** Do not use logic like `@if($theme == 'bootstrap')` in Blade files.
* **Micro-files Strategy:** Table views are divided into single-purpose micro-files (e.g., `index.blade.php`, `tbody.blade.php`, `td.blade.php`).
* **Directives:** Rely strictly on `@theme()` directives.

## 4. Server-Side Actions

* **No Cache or JS processing for Actions:** Action logic and buttons are no longer processed via JavaScript or cached.
* **Full Blade Rendering:** Actions are fully rendered as server-side Blade components/fragments via `renderActions()`.

## 5. Performance & Partials

* **Livewire DOM Isolation:** Use the `power-components/partials` fragments to isolate DOM updates and boost performance.
* **Hot Zones:** There are three demarcated Hot Zones for updates:
  1. `pg-tbody`
  2. `pg-pagination`
  3. `pg-filters`

## 6. Testing Strategy

* **Self-sufficient Tests:** Tests must be independent and immune to themes.
* **Core Focus:** Focus testing strictly on core engine capabilities: Search, Filters, Order, and Pagination.
* **No Global Fixtures:** Build mini-components at runtime within the test files instead of using global fixtures.

---

## 7. Skills

PowerGrid 7.x provides specialized [Agent Skills](https://agentskills.io/home) for common tasks. Skills are located in `.ai/skills/` and provide detailed, step-by-step instructions for complex operations.

### Available Skills

| Skill | Use When |
|-------|----------|
| `powergrid-theme` | Creating a new theme OR updating an existing theme |
| `upgrade-theme-classfrom-v6-to-v7` | Migrating a v6 Theme class to v7 architecture |
| `create-powergrid-plugin` | Creating a new PowerGrid plugin (Column behavior with Blade/JS/events) |

### Skill Triggers

#### powergrid-theme

Use this skill when the user asks to "create a theme", "add a new theme", "update theme X", "modify theme classes".

Examples:
- "Create a new PrimeReact theme"
- "Update the Tailwind theme to add dark mode support"
- "I need a custom theme for Vuetify"

#### upgrade-theme-classfrom-v6-to-v7

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
2. **Load the skill:** Read the corresponding `.ai/skills/<skill-name>/SKILL.md` file
3. **Follow the workflow:** Skills contain detailed step-by-step instructions -- follow them precisely
4. **Use provided resources:** Skills may reference additional files (e.g., `REFERENCE.md`) in the same directory
5. **Complete all steps:** Ensure all checklist items in the skill are completed before marking the task done
