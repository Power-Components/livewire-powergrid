#!/bin/bash

LARAVEL_VERSION=$(composer show 'laravel/framework' | grep 'versions' | grep -o -E '\*\ .+' | cut -d' ' -f2 | cut -d',' -f1)

if [[ $LARAVEL_VERSION =~ .*v9.* ]]; then
  ./vendor/bin/phpstan analyse --ansi --memory-limit=-1
fi
