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
            //Remove anything but alphanumeric, dot and all slashes
            ->replaceMatches('#[^A-Za-z0-9 .//\\\\]#', '')
            //Convert multiple spaces into forward slashes
            ->replaceMatches('/\s+/', '//')
            //multiple back slashes into forward slashes
            ->replaceMatches('/\\\{2,}/', "\\")
            //Multiple forward slashes
            ->replaceMatches('/\/{2,}/', "\\")
            //Multile dots
            ->replaceMatches('/\.{2,}/', ".")
            //Left over dots into forward slashes
            ->replace('.', '\\')
            //Left over backslahes into forward slashes
            ->replace('/', '\\')
            ->rtrim('\\')
            ->toString();
    }
}
