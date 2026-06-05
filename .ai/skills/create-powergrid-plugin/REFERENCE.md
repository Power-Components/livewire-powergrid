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
    public function handles(Column|array $column): bool { return false; }
    public function render(Column|array $column, mixed $row): ?string { return null; }
    public static function themeTokens(): array { return []; }
}
```

### How Plugins Are Resolved

1. ServiceProvider calls `$plugin::boot()` (registers Column macro)
2. `PowerGridComponent::resolvePlugins()` instantiates each plugin, calls `isEnabled()`
3. During render, `renderColumnContent($column, $row)` iterates plugins: `handles()` then `render()`
4. `getListeners()` scans `#[On]` attributes via reflection
5. Events routed to `pgPluginListener` (generic) or explicit proxy methods

### Livewire Event Flow

1. Alpine.js: `$wire.dispatch('pg:{eventName}-' + tableName, [field, id, value])`
2. Plugin: `#[On('pg:{eventName}-{tableName}')]` attribute on listener method
3. `getListeners()` maps event to `pgPluginListener` (no proxy needed for external plugins)
4. Plugin method calls user hook: `$this->component->onUpdated{PluginName}($id, $field, $value)`

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

    public function handles(Column|array $column): bool
    {
        return ! empty(data_get($column, 'pluginData.{pluginKey}'));
    }

    public function render(Column|array $column, mixed $row): ?string
    {
        return view('powergrid-plugins::{PluginName}.index', [
            'tableName' => $this->component->tableName,
            'primaryKey' => $this->component->realPrimaryKey,
            'row' => $row,
            'column' => $column,
            'config' => data_get($column, 'pluginData.{pluginKey}'),
            'js' => file_get_contents(__DIR__.'/index.js'),
        ])->render();
    }

    #[On('pg:{eventName}-{tableName}')]
    public function {listenerMethod}(mixed ...$params): void
    {
        [$field, $id, $value] = $params;

        $this->component->onUpdated{PluginName}($id, $field, $value);
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
    'js' => null,
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

@once
<script>
    {!! $js !!}
</script>
@endonce

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
    PowerGrid::plugins([
        FlatpickrPlugin::class,
        EditablePlugin::class,
        ToggleablePlugin::class,
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

---
