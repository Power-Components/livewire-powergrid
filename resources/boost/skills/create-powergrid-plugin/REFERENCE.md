# PowerGrid Plugin Reference

## Architecture

### Plugin Base Class (`PluginBase`)

```php
abstract class PluginBase implements Wireable
{
    public function __construct(protected PowerGridComponent $component) {}
    public static function boot(): void {}
    public static function ruleModifiers(): array { return []; }
    public function processRuleModifiers(array $rule, bool $apply): array { return []; }
    abstract public function name(): string;
    abstract public function isEnabled(): bool;
    public function handles(Column|array|\stdClass $column): bool { return false; }
    public function render(Column|array|\stdClass $column, mixed $row): ?string { return null; }
    public function scripts(): array { return []; } // JS files, relative to the plugin dir
    public function styles(): array { return []; } // CSS files, relative to the plugin dir
    public static function themeTokens(): array { return []; }
}
```

### How Plugins Are Resolved

1. ServiceProvider calls `$plugin::boot()` (registers Column macro)
2. `PowerGridComponent::resolvePlugins()` instantiates each plugin, calls `isEnabled()`
3. During render, `renderColumnContent($column, $row)` iterates plugins: `handles()` then `render()`
4. `getListeners()` scans `#[On]` attributes via reflection
5. Events routed to `pgPluginListener` (generic) or explicit proxy methods
6. `Theme::resolveTokens()` merges every registered plugin's `themeTokens()` after the theme's section methods and before `config('livewire-powergrid.theme_overrides')`

### Livewire Event Flow

1. Alpine.js: `$wire.dispatch('pg:{eventName}-' + tableName, [field, id, value])`
2. Plugin: `#[On('pg:{eventName}-{tableName}')]` attribute on listener method
3. `getListeners()` maps event to `pgPluginListener` (no proxy needed for external plugins)
4. Plugin method calls user hook: `$this->component->onUpdated{PluginName}($id, $field, $value)`

---

## Theme-aware plugins

A plugin that ships UI should contribute **tokens** (classes) and **views** (markup) without `instanceof` theme branching. `Theme::resolveTokens()` already walks `PowerGridManager::$plugins` and merges each `PluginBase::themeTokens()`.

### `PluginBase::themeTokens()`

Override the static method to return a nested token slice. This array is merged as-is (it is **not** run through `HasProperties::toArray()`), so keys must already be the snake_cased paths Blade reads via `theme()`:

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

The `tabs` group is the first real example of this surface. Tokens live under `tabs.*` (`list`, `tab`, `tab_active`, `tab_inactive`, `badge`, `badge_active`, `badge_inactive`, optional `view`) and are read by the shared base blade `resources/views/components/themes/tailwind/tabs.blade.php`. Themes that need different **classes** override `tabs()`; themes that need different **HTML** set `tabs.view` (Flux → `powergrid-plugins::Tabs.themes.flux`). A plugin that owns a similar widget can inject the same kind of slice from `themeTokens()` so every theme picks it up.

Merged **after** the theme's own section methods and **before** `config('livewire-powergrid.theme_overrides')`, so a no-code override still wins.

### `theme_view()` + `powergrid-plugins::` fallback

Mirror `src/Plugins/FilterBuilder/FilterBuilderPlugin.php::resolveThemeView()` (Export uses the same shape; `HasTabs::tabsView()` is the tabs equivalent):

1. Try `theme_view($alias)` — respects the active theme's tokens, `baseView`, and `parentTheme` fallback in `Theme::doResolveView()`.
2. If that view is empty or missing, fall back to a packaged view under the `powergrid-plugins::` namespace.

```php
private function resolveThemeView(): string
{
    $tokenView = theme_view('header.my_plugin');

    if ($tokenView !== '' && view()->exists($tokenView)) {
        return $tokenView;
    }

    $fallback = 'powergrid-plugins::{PluginName}.themes.index';

    return view()->exists($fallback) ? $fallback : '';
}
```

Do **not** add `instanceof DaisyUI` / `instanceof Flux` branches in new plugins. If a theme needs different markup, point a `*.view` token at the packaged blade (Flux does this for tabs) instead of switching on the theme class.

Layout for packaged views:

```
app/PowerGrid/Plugins/{PluginName}/
  {PluginName}Plugin.php
  index.blade.php              # column-cell view (existing)
  themes/
    index.blade.php            # token-driven default (Tailwind / DaisyUI)
    flux.blade.php             # only when HTML genuinely differs
```

Register the view namespace with `loadViewsFrom(..., 'powergrid-plugins')` as in the ServiceProvider template below. The cell view stays `powergrid-plugins::{PluginName}.index`; the chrome/trigger view is what `resolveThemeView()` selects.

---

## Templates

### Plugin PHP Class

```php
<?php

namespace App\PowerGrid\Plugins\{PluginName};

use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;

class {PluginName}Plugin extends PluginBase
{
    public static function boot(): void
    {
        Column::macro('{macroName}', function (/* params */): Column {
            /** @var Column $this */
            $this->pluginData['{pluginKey}'] = [
                // store config here
            ];

            return $this;
        });
    }

    public function name(): string
    {
        return '{pluginKey}';
    }

    public function isEnabled(): bool
    {
        return collect($this->component->columns)
            ->contains(fn ($column) => ! empty(data_get($column, 'pluginData.{pluginKey}')));
    }

    public function handles(Column|array|\stdClass $column): bool
    {
        return ! empty(data_get($column, 'pluginData.{pluginKey}'));
    }

    public function scripts(): array
    {
        return ['index.js'];
    }

    /**
     * Optional. Nested token slice merged into every theme
     * (Theme::resolveTokens()). Leave the default [] when the plugin
     * has no chrome of its own.
     */
    public static function themeTokens(): array
    {
        return [
            // 'my_plugin' => ['wrapper' => '...', 'button' => '...'],
        ];
    }

    public function render(Column|array|\stdClass $column, mixed $row): ?string
    {
        return view('powergrid-plugins::{PluginName}.index', [
            'tableName' => $this->component->tableName,
            'primaryKey' => $this->component->realPrimaryKey,
            'row' => $row,
            'column' => $column,
            'config' => data_get($column, 'pluginData.{pluginKey}'),
        ])->render();
    }

    #[On('pg:{eventName}-{tableName}')]
    public function {listenerMethod}(mixed ...$params): void
    {
        $field = $params[0] ?? null;
        $id    = $params[1] ?? null;
        $value = $params[2] ?? null;

        // Guard: ignore events for fields this plugin does not handle
        // (isHandledField() is provided by PluginBase and calls handles()).
        if (! is_string($field) || ! is_scalar($id) || ! is_scalar($value) || ! $this->isHandledField($field)) {
            return;
        }

        $this->component->onUpdated{PluginName}((string) $id, $field, (string) $value);
    }
}
```

### Blade View

```blade
@props([
    'primaryKey' => null,
    'row' => null,
    'column' => null,
    'tableName' => null,
    'config' => null,
])

@php
    $fieldName = data_get($column, 'field');
    $currentValue = data_get($row, $fieldName);

    $params = [
        'tableName' => $tableName,
        'id' => data_get($row, $primaryKey),
        'field' => $fieldName,
    ];
@endphp

<div
    wire:key="pg-{pluginKey}-{{ data_get($row, $primaryKey) }}-{{ $fieldName }}"
    x-data="pg{PluginName}(@js($params))"
>
    {{-- Render initial state SERVER-SIDE, not with x-model --}}
</div>
```

### Alpine.js Component

```javascript
if (!window.pg{PluginName}Registered) {
    const register = () => {
        window.Alpine.data('pg{PluginName}', (params) => ({
            tableName: params.tableName,
            id: params.id,
            field: params.field,

            onChange(newValue) {
                // MUST use array format, NOT object
                this.$wire.dispatch('pg:{eventName}-' + this.tableName, [
                    this.field,
                    this.id,
                    newValue
                ]);
            },
        }));
    };

    if (window.Alpine) {
        register();
    } else {
        document.addEventListener('alpine:init', () => {
            register();
        });
    }
    window.pg{PluginName}Registered = true;
}
```

### ServiceProvider

```php
<?php

namespace App\Providers;

use App\PowerGrid\Plugins\{PluginName}\{PluginName}Plugin;
use Illuminate\Support\ServiceProvider;

class PowerGridPluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register view namespace for plugin blade files
        $this->loadViewsFrom(
            app_path('PowerGrid/Plugins'),
            'powergrid-plugins'
        );

        // Register Column macro
        {PluginName}Plugin::boot();
    }
}
```

### bootstrap/providers.php

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\PowerGridPluginServiceProvider::class,
];
```

### AppServiceProvider (plugin registration)

```php
use App\PowerGrid\Plugins\{PluginName}\{PluginName}Plugin;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

public function boot(): void
{
    // Built-in plugins (Editable, Export, FilterBuilder, Flatpickr, Toggleable)
    // are always registered automatically and de-duplicated by PowerGrid::plugins().
    // Only add your custom plugin here.
    PowerGrid::plugins([
        {PluginName}Plugin::class,
    ]);
}
```

### User Component Hook

```php
public function onUpdated{PluginName}(string|int $id, string $field, string $value): void
{
    // IMPORTANT: field must be in Model's $fillable
    MyModel::find($id)->update([$field => $value]);
}
```

---

## Variable Reference

| Placeholder | Description | Example |
|---|---|---|
| `{PluginName}` | PascalCase plugin name | `SelectRow` |
| `{pluginKey}` | camelCase key for pluginData | `selectRow` |
| `{macroName}` | Column method name | `selectRow` |
| `{eventName}` | Livewire event name segment | `selectRow` |
| `{listenerMethod}` | PHP method name for listener | `selectRowChanged` |
| `{PluginName}Plugin` | Full class name | `SelectRowPlugin` |

---

## Common Pitfalls

| Problem | Cause | Fix |
|---------|-------|-----|
| "Method Column::x does not exist" | `boot()` not called | Ensure ServiceProvider calls `Plugin::boot()` and is registered |
| "View not found" | Missing view namespace | Add `loadViewsFrom(app_path('PowerGrid/Plugins'), 'powergrid-plugins')` |
| Event not reaching backend | Object format in dispatch | Use array: `[field, id, value]` not `{field, id, value}` |
| Initial value not shown | Using `x-model` + `x-for` | Render options server-side with `@foreach` + `@selected()` |
| Update silently ignored | Field not fillable | Add field to Model's `$fillable` or `#[Fillable([...])]` |
| Need proxy in package | Old approach | Not needed - `pgPluginListener` handles external plugins generically |
| JS loaded on every table | Inlined in the Blade view | Return files from `scripts()` / `styles()` so PowerGrid injects them only when the plugin is enabled (and minifies them in production) |
| `theme('my_plugin.tab_active')` empty | camelCase keys in `themeTokens()` | `themeTokens()` is not snake_cased; use `tab_active`, not `tabActive` |

---
