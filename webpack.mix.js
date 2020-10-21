let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Custom Mix setup
 |--------------------------------------------------------------------------
 |
 */
/*
mix.webpackConfig({
    devtool: 'source-map'
});
*/

mix
    .setPublicPath('public')
    .js('resources/assets/js/survey-manager.js', 'public/js/')
    .js('resources/assets/js/survey-front.js', 'public/js/');
/*mix.extract([
    'jquery',
    'bootstrap',
    'popper.js',
    'axios',
    'vue',
], 'public/js/frontend/vendor.js')
    .sourceMaps();*/

