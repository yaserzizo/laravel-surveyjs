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
    .js('resources/assets/js/survey-front.js', 'public/js/');/*.sourceMaps()
    .version();
*/
