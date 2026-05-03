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
        $struct = $theme->struct();

        $flatStruct = Arr::dot($struct);
        $keys = array_keys($flatStruct);

        $keysString = implode(",\n        ", array_map(function ($key) {
            return "'".$key."'";
        }, $keys));

        $content = <<<PHP
<?php

namespace PHPSTORM_META {
    expectedArguments(
        \\theme(),
        0,
        $keysString
    );

    expectedArguments(
        \\PowerComponents\LivewirePowerGrid\Support\ThemeManager::theme(),
        0,
        $keysString
    );
}

PHP;

        file_put_contents(base_path('.phpstorm.meta.php'), $content);

        $this->components->info('Generated .phpstorm.meta.php file.');
    }
}
