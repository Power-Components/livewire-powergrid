# PowerGrid 7.x Agents Guidelines

This document outlines the architectural changes and guidelines for AI agents working with **PowerGrid 7.x**, based on the master migration plan.

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

*Keep these guidelines in mind when refactoring, creating new features, or writing tests for PowerGrid 7.x.*

## Skills
Available skills for the agent are located in the `.ai/skills/` directory. To register a skill, ensure its file name and description are documented. Currently available:
- **powergrid-theme**: Create or update a PowerGrid theme using ThemeManager.
- **upgrade-theme-classfrom-v6-to-v7**: Migrates a v6 Theme class to the v7 unified struct() architecture.
