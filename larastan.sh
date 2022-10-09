#!/bin/bash

LARAVEL_VERSION="$(composer show 'laravel/framework' --format=json | jq -r '."versions"[0] | sub("v";"")')"

if { echo "9.0.0"; echo "${LARAVEL_VERSION}"; } | sort --version-sort --check 2>/dev/null; then
  ./vendor/bin/phpstan analyse --ansi --memory-limit=-1
fi
