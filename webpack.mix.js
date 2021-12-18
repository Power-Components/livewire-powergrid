const mix = require('laravel-mix')

mix.js('js/index.js', 'dist/powergrid.js')
    .setPublicPath('dist')

if (mix.inProduction()) {
    mix.version()
}
