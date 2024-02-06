<?php

namespace PowerComponents\LivewirePowerGrid\Exceptions;

final class InvalidTableNameException extends \Exception
{
    /**
     * @phpstan-return never
     *
     * @throws \Exception
     */
    public static function throw($message): never
    {
        throw new self($message);
    }
}
