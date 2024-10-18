let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js('resources/assets/js/app.js', 'public/js')
//    .sass('resources/assets/sass/app.scss', 'public/css');


mix.sass('resources/assets/sass/app.scss', 'public/css/scss-app.css')
.styles([
    // 'public/css/argon.css',
    // 'resources/assets/css/style.css',
    'public/css/scss-app.css'
], 'public/css/all.css')

.js([
	'resources/assets/js/app.js',
    // 'node_modules/popper.js/dist/popper.js.map',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'public/vendor/chart.js/dist/Chart.min.js',
    'public/vendor/chart.js/dist/Chart.extension.js',
    'public/js/argon.js?'
], 'public/js/all.js')
.browserSync('http://sfa.local');