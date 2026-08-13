<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

class GenerateThemeMetaCommand extends Command
{
    protected $signature = 'powergrid:generate-theme-meta';

    protected $description = 'Generate PhpStorm meta file for PowerGrid theme function autocomplete';

    public function handle(): void
    {
        $theme = new Tailwind();

        $flatStruct = Arr::dot($theme->resolveTokens());
        $tokenKeys = array_keys($flatStruct);

        $viewAliases = $this->collectViewAliases($theme, $flatStruct);

        $tokenKeysString = $this->formatKeys($tokenKeys);
        $viewAliasesString = $this->formatKeys($viewAliases);

        $content = <<<PHP
<?php

namespace PHPSTORM_META {
    expectedArguments(
        \\theme(),
        0,
        $tokenKeysString
    );

    expectedArguments(
        \\PowerComponents\LivewirePowerGrid\Support\ThemeManager::theme(),
        0,
        $tokenKeysString
    );

    expectedArguments(
        \\theme_view(),
        0,
        $viewAliasesString
    );

    expectedArguments(
        \\PowerComponents\LivewirePowerGrid\Support\ThemeManager::view(),
        0,
        $viewAliasesString
    );
}

PHP;

        file_put_contents(base_path('.phpstorm.meta.php'), $content);

        $this->components->info('Generated .phpstorm.meta.php file.');
    }

    /**
     * @param  array<string, mixed>  $flatTokens
     * @return list<string>
     */
    private function collectViewAliases(Tailwind $theme, array $flatTokens): array
    {
        $aliases = [];

        foreach (array_keys($flatTokens) as $key) {
            if ($key === 'base_view') {
                continue;
            }

            if (str_ends_with($key, '.view')) {
                $aliases[] = substr($key, 0, -5); // strip '.view'
            }

            if (str_contains($key, '.view_')) {
                $alias = preg_replace('/\.view_/', '.', $key, 1);
                if ($alias !== null) {
                    // Blade uses hyphenated names: view_inline_filters → inline-filters
                    $aliases[] = str_replace('_', '-', str_replace('table.', 'table.', $alias));
                }
            }
        }

        $canonicalAliases = [
            'header',
            'header.search',
            'header.toggle-columns',
            'header.soft-deletes',
            'header.enabled-filters',
            'header.filters',
            'header.loading',
            'header.message-soft-deletes',
            'header.multi-sort',
            'footer',
            'pagination',
            'table.header',
            'table.row',
            'table.cols',
            'table.th-empty',
            'table.inline-filters',
            'table.checkbox-all',
            'table.checkbox-row',
            'table.radio-row',
            'table.footer-summarize',
            'table.header-summarize',
            'table.responsive-container',
            'toggle-detail',
            'toggle-detail-responsive',
            'editable',
            'toggleable',
            'filter.boolean',
            'filter.date_picker',
            'filter.input_text',
            'filter.number',
            'filter.select',
            'filter.flyout',
        ];

        $aliases = array_unique(array_merge($aliases, $canonicalAliases));
        sort($aliases);

        return $aliases;
    }

    /**
     * @param  list<string>  $keys
     */
    private function formatKeys(array $keys): string
    {
        return implode(",\n        ", array_map(fn (string $key) => "'{$key}'", $keys));
    }
}
