#!/bin/bash

cd tests/cypress/app &&

# | ------------------------- |
# | Environment
# | ------------------------- |

cp .env.example .env

# | ------------------------- |
# | install local PowerGrid
# | ------------------------- |
composer_json="composer.json"
new_minimum_stability="dev"
new_repositories='[
    {
        "type": "path",
        "url": "../../../"
    }
]'

jq --arg new_minimum_stability "$new_minimum_stability" \
   --argjson new_repositories "$new_repositories" \
   '. += {
      "minimum-stability": $new_minimum_stability,
      "repositories": $new_repositories
   }' "$composer_json" > tmp_composer.json

mv tmp_composer.json "$composer_json"

composer require power-components/livewire-powergrid

# | ------------------------- |
# | build
# | ------------------------- |
php artisan key:generate

npm install

npm run build
