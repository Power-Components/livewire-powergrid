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

## 7. Skills & Specialized Workflows

PowerGrid 7.x provides specialized skills for common tasks. Skills are located in `.ai/skills/` and provide detailed, step-by-step instructions for complex operations.

### When to Use Skills

**ALWAYS use the appropriate skill** when the user's request matches one of the following scenarios:

#### Available Skills

1. **powergrid-theme**
   - **Use when:** Creating a new theme OR updating an existing theme
   - **Triggers:** User asks to "create a theme", "add a new theme", "update theme X", "modify theme classes"
   - **Examples:**
     - "Create a new PrimeReact theme"
     - "Update the Tailwind theme to add dark mode support"
     - "I need a custom theme for Vuetify"
   - **How to invoke:** `skill('powergrid-theme')`

2. **upgrade-theme-classfrom-v6-to-v7**
   - **Use when:** Migrating a v6 Theme class to v7 architecture
   - **Triggers:** User mentions "migrate", "upgrade from v6", "convert v6 theme", "update old theme"
   - **Examples:**
     - "Migrate my Bootstrap theme from v6 to v7"
     - "Upgrade the old Tailwind theme class to the new struct() format"
     - "Convert this v6 theme to v7"
   - **How to invoke:** `skill('upgrade-theme-classfrom-v6-to-v7')`

### Skill Usage Protocol

1. **Recognize the trigger:** Identify if the user's request matches a skill scenario
2. **Load the skill:** Use the `skill()` tool with the appropriate skill name
3. **Follow the workflow:** Skills contain detailed step-by-step instructions—follow them precisely
4. **Use provided resources:** Skills may reference scripts, templates, or validation tools
5. **Complete all steps:** Ensure all checklist items in the skill are completed before marking the task done

### Example Usage

```
User: "I need to create a new Bulma theme for PowerGrid"

Agent: I'll use the powergrid-theme skill to guide this process.
[Loads skill('powergrid-theme')]
[Follows the skill's step-by-step workflow]
[Creates theme class, updates ThemeManager, runs tests]
```

### Registering New Skills

To add a new skill:
1. Create a `.md` file in `.ai/skills/`
2. Document the skill name and description in this section
3. Include clear triggers and examples
4. Provide a detailed step-by-step workflow in the skill file
