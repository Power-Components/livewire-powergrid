const mix = require('laravel-mix')

mix.js('resources/js/index.js', 'powergrid.js')
    .css('resources/css/tailwind.css', 'tailwind.css')
    .css('resources/css/bootstrap.css', 'bootstrap5.css')
    .setPublicPath('dist')

if (mix.inProduction()) {
    mix.version()
}
