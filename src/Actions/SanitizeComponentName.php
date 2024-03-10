<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

class SanitizeComponentName
{
    public static function handle(string $componentName): string
    {
        return str($componentName)
            ->rtrim('.php')
            // Remove possible prefix
            ->ltrim('//')
            //Remove anything but alphanumeric, dot and slashes
            ->replaceMatches('#[^A-Za-z0-9 .//\\\\]#', '')
            //Convert multiple spaces into forward slashes
            ->replaceMatches('/\s+/', '//')
            //multiple back slashes into forward slashes
            ->replaceMatches('/\\\{2,}/', "\\")
            //Multiple forward slashes
            ->replaceMatches('/\/{2,}/', "\\")
            //Multile dots
            ->replaceMatches('/\.{2,}/', ".")
            ->replace('.', '\\')
            //Left over backslahes into forward slashes
            ->replace('/', '\\')
            ->rtrim('\\')
            ->toString();
    }
}
