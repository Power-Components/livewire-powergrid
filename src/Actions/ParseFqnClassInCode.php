<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

final class ParseFqnClassInCode
{
    /**
     * Parse namespace from PHP source code
     * Inspired by: https://gist.github.com/ludofleury/1886076
     * @throws \Exception
     */
    public static function handle(string $sourceCode): string
    {
        if (preg_match('#^namespace\s+(.+?);.*class\s+(\w+).+;$#sm', $sourceCode, $matches)) {
            return $matches[1] . '\\' . $matches[2];
        }

        throw new \Exception('could not find a FQN Class is source-code');
    }
}
