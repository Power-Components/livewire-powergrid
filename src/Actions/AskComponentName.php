<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\File;

use function Laravel\Prompts\{confirm, text};

final class AskComponentName
{
    private static string $componentName = '';

    public static function handle(): string
    {
        while (self::$componentName === '') {
            self::$componentName = SanitizeComponentName::handle(
                text(
                    label: 'Enter a name for your new PowerGrid Component:',
                    placeholder: 'UserTable',
                    default: 'UserTable',
                    required: true
                )
            );

            self::checkIfComponentAlreadyExists();
        }

        return self::$componentName;
    }

    private static function checkIfComponentAlreadyExists(): void
    {
        if (File::exists(powergrid_components_path(self::$componentName . '.php'))) {
            $confirmation = (bool) confirm(
                "Component [" . self::$componentName . "] already exists. Overwrite it?",
                default: false,
                hint: '❗ WARNING ❗'
            );

            if ($confirmation === false) {
                self::$componentName = '';
            }
        }
    }
}
