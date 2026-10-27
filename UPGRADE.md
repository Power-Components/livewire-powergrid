# Upgrading PowerGrid from v6 to v7

The full guide lives at https://livewire-powergrid.com/release-notes-and-upgrade/upgrade-guide.html.
This file lists the changes most likely to break a v6 app silently.

## Requirements

- PHP 8.3+, Laravel 12+, Livewire 4, Tailwind CSS 4.
- `openspout/openspout:^5.0` is only suggested. Require it for XLSX/CSV export; without it the XLS option is hidden.

## Re-publish the config

```bash
php artisan vendor:publish --tag=livewire-powergrid-config --force
```

The v6 file references classes that no longer exist (`Themes\Bootstrap5`, `openspout_v4`, `Components\Exports\...`).

## Silent changes

| v6 | v7 |
| --- | --- |
| Persistence opt-in via `persist()` | On by default for `columns`, `filters`, `sorting` (`pg7:` keys). Use `withoutPersist()` to opt out |
| `'filter' => 'outside'` | Invalid; filters stop rendering. Use `inline`, `dropdown`, `flyout` or `null` |
| Writing `$this->filters[...]` | Field records via `putFilterRecord()` + `commitFilters()`; `$enabledFilters` is derived |
| `onUpdatedEditable()` / `onUpdatedToggleable()` | Never called. Use `onPluginUpdated()` |
| `->icon('default-trash')` | Blade component name, e.g. `->icon('svg.trash')` |
| HTML returned from field closures | Escaped (`escape_closure_fields`). Return `HtmlString` for trusted HTML |
| camelCase keys in theme arrays / `theme_overrides` | Ignored. Use snake_case (`th_actions`) |
| `Livewire::test()` sees fresh rows | Partial renders. Set `POWERGRID_PARTIALS_ENABLED=false` in `phpunit.xml` |
| flatpickr / TomSelect / SlimSelect loaded by PowerGrid | Install and expose on `window` (`window.flatpickr`, `window.TomSelect`, `window.SlimSelect`) |
| `plugins.select.*.cdn` / `css` | Removed; the `slim` / `tom` arrays are constructor options |
| Date filter `params(['timezone' => ...])` ignored | Range read in that timezone, converted to `app.timezone` |
