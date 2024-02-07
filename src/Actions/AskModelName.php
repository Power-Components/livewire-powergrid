<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use function Laravel\Prompts\{error, suggest};

final class AskModelName
{
    protected static string $model = '';

    protected static string $fqn = '';

    /**
     * @return array{model: string, fqn: string}
     */
    public static function handle(): array
    {
        {
            while (self::$model === '') {
                self::setModel(suggest(
                    label: 'Enter your Model name',
                    options: ListModels::handle(),
                    default: 'User',
                    required: true,
                ));

                self::parseFqn();
                self::checkIfModelExists();
            }

            return ['model' => self::$model, 'fqn' => self::$fqn];
        }
    }

    private static function setModel(string $model): void
    {
        self::$model = str($model)->replaceMatches('#[^A-Za-z0-9\\\\]#', '')->toString();
    }

    private static function parseFqn(): void
    {
        self::$fqn = 'App\\Models\\' . self::$model;

        if (str_contains(self::$model, '\\')) {
            self::$fqn   = self::$model;
            self::$model = str(self::$fqn)->rtrim('\\')->afterLast('\\');
        }
    }

    private static function checkIfModelExists(): void
    {
        if (!class_exists(self::$fqn)) {
            error("Could not find [" . self::$fqn . "] class. Try again or press Ctrl+C to abort.");

            self::$model = '';

            self::$fqn = '';
        }
    }
}
