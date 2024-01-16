<?php

arch('debug functions are not used')
    ->expect(['dump', 'dd', 'ddd', 'kint', 'ray', 'ds', 'var_dump', 'print_r', 'exit', 'die'])
    ->not->toBeUsed();

arch('PowerGrid do not use to Strict Types')
    ->expect('\PowerComponents\LivewirePowerGrid')
    ->not->toUseStrictTypes();
