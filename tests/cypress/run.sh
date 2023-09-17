#!/bin/bash

git clone https://github.com/Power-Components/powergrid-demo-misc.git app

# | ------------------------- |
# | Environment
# | ------------------------- |

cp app/.env.example app/.env

cat >> app/.env <<EOF

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=powergridtest
DB_USERNAME=root
DB_PASSWORD=password

EOF

# | ------------------------- |
# | install Livewire 3
# | ------------------------- |
cd app && composer require livewire/livewire ^3.0

# | ------------------------- |
# | install local PowerGrid
# | ------------------------- |
composer remove power-components/livewire-powergrid

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

php artisan migrate:fresh --seed --force
