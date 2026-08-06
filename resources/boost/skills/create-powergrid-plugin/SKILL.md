---
description: >
    Create a complete PowerGrid plugin from scratch, including PHP class,
    Column macro, Blade view, Alpine.js component, and registration.
name: create-powergrid-plugin
---

## What I do

Scaffold a complete external PowerGrid plugin in the user's project (NOT in the package).

## When to use me

- A new interactive column behavior is needed (e.g., selectable, colorpicker, rating)
- The behavior requires its own Blade view and/or Alpine.js component
- The plugin needs to respond to user interactions via Livewire events

## Quick Overview

Plugins are created in the user's project at `app/PowerGrid/Plugins/{PluginName}/`.
They do NOT require modifying the PowerGrid package.

### Files to create:

1. `app/PowerGrid/Plugins/{PluginName}/{PluginName}Plugin.php` - Plugin class
2. `app/PowerGrid/Plugins/{PluginName}/index.blade.php` - Blade template
3. `app/PowerGrid/Plugins/{PluginName}/index.js` - Alpine.js component
4. `app/Providers/PowerGridPluginServiceProvider.php` - ServiceProvider

### Files to modify:

1. `bootstrap/providers.php` - Register the ServiceProvider
2. `app/Providers/AppServiceProvider.php` - Add plugin to `PowerGrid::plugins([...])`
3. User's PowerGrid component - Add column + hook method

## Critical Rules

1. **Create plugins in user's project**, never in the powergrid package
2. **ServiceProvider must call `boot()`** and register view namespace with `loadViewsFrom()`
3. **Alpine.js dispatch must use array format**: `[field, id, value]` (NOT object `{field, id, value}`)
4. **Render initial values server-side** with `@foreach`/`@selected()` - do NOT use `x-model` for initial state
5. **Model `$fillable` must include** the field being updated
6. **No proxy method needed** - `pgPluginListener` in Listeners.php handles routing generically

## Workflow

1. Gather requirements (plugin name, macro signature, interaction type, hook name)
2. Read `REFERENCE.md` in this skill directory for templates and full details
3. Create the plugin files following the templates
4. Register ServiceProvider and plugin
5. Add column + hook to user's component
6. Verify it works

For complete templates, architecture details, and troubleshooting, see:
`REFERENCE.md`
