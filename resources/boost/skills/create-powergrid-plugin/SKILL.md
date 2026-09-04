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
7. **Theme-aware markup uses tokens, not `instanceof`.** Contribute classes via `PluginBase::themeTokens()`; resolve views with `theme_view($alias)` and fall back to `powergrid-plugins::{PluginName}.themes.*`.

## Theme-aware plugins

Plugins can inject CSS-class tokens and ship theme-aware Blade without theme conditionals.

**`PluginBase::themeTokens()`** is merged into every theme in `Theme::resolveTokens()` (after the theme's own section methods, before `config('livewire-powergrid.theme_overrides')`). It is a raw array (not run through `HasProperties::toArray()`), so keys must already be snake_cased. The `tabs` group (`list` / `tab` / `tab_active` / `tab_inactive` / `badge` / `badge_active` / `badge_inactive` / optional `view`) is the first real example of a plugin-shaped token surface:

```php
public static function themeTokens(): array
{
    return [
        'my_plugin' => [
            'wrapper' => 'flex items-center gap-2',
            'button'  => 'rounded-md px-2 py-1 text-sm',
        ],
    ];
}
```

**View resolution** follows `src/Plugins/FilterBuilder/FilterBuilderPlugin.php::resolveThemeView()` (and `HasTabs::tabsView()`): try `theme_view($alias)`, then fall back to a packaged `powergrid-plugins::` view. Flux-style themes select a different blade by setting a `*.view` token (e.g. Flux `tabs.view` → `powergrid-plugins::Tabs.themes.flux`); do not branch on `instanceof DaisyUI` / `instanceof Flux` in new plugins.

```php
private function resolveThemeView(): string
{
    $tokenView = theme_view('header.my_plugin'); // or 'tabs', 'header.export', ...

    if ($tokenView !== '' && view()->exists($tokenView)) {
        return $tokenView;
    }

    $fallback = 'powergrid-plugins::{PluginName}.themes.index';

    return view()->exists($fallback) ? $fallback : '';
}
```

Ship one shared token-driven blade (Tabs: `resources/views/components/themes/tailwind/tabs.blade.php` reads `theme('tabs.*')`) and only add a `themes/flux.blade.php` (etc.) when the HTML genuinely differs.

See `REFERENCE.md` for the full `themeTokens()` / `resolveThemeView()` templates.

## Workflow

1. Gather requirements (plugin name, macro signature, interaction type, hook name)
2. Read `REFERENCE.md` in this skill directory for templates and full details
3. Create the plugin files following the templates
4. Register ServiceProvider and plugin
5. Add column + hook to user's component
6. Verify it works

For complete templates, architecture details, and troubleshooting, see:
`REFERENCE.md`
