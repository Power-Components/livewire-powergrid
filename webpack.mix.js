const mix = require('laravel-mix')

mix.js('resources/js/index.js', 'powergrid.js')
    .css('resources/css/style.css', 'powergrid.css')
    .setPublicPath('dist')

if (mix.inProduction()) {
    mix.version()
}
