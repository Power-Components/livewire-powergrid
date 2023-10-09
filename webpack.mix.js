const mix = require('laravel-mix')

mix.js('resources/js/index.js', 'powergrid.js')
    .css('resources/css/style.css', 'powergrid.css')
    .css('resources/css/tom-select.css', 'tom-select.css')
    .css('resources/css/tailwind.css', 'tailwind.css')
    .css('resources/css/bootstrap.css', 'bootstrap5.css')
    .setPublicPath('dist')

if (mix.inProduction()) {
    mix.version()
}
