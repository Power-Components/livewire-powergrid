#!/bin/bash

git clone https://github.com/Power-Components/powergrid-demo-misc.git app &

cd app &&

# | ------------------------- |
# | Environment
# | ------------------------- |

cp .env.example .env

cat >> .env <<EOF
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=powergridtest
DB_USERNAME=root
DB_PASSWORD=password
EOF

# | ------------------------- |
# | copy stubs
# | ------------------------- |

ls -lh

sudo chmod -R 755 app
sudo chmod -R 755 resources

cp tests/cypress/stubs/CypressTable.php tests/cypress/app/app/Livewire/CypressTable.php
cp tests/cypress/stubs/rules-controllers.blade.php tests/cypress/app/resources/views/components/header/rules-controllers.blade.php

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
rm composer.lock

composer require power-components/livewire-powergrid

# | ------------------------- |
# | build
# | ------------------------- |
php artisan key:generate

npm install

npm run build

php artisan migrate:fresh --seed --force
