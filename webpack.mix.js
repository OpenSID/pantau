const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

// Security: bind dev server to localhost and disable hot module replacement to reduce exposure
// (advisory: GHSA-9jgg-88mc-972h / GHSA-4v9v-hfq4-rm2v)
mix.webpackConfig({
    devServer: {
        host: '127.0.0.1',
        allowedHosts: 'none',
        client: {
            webSocketURL: 'ws://127.0.0.1:8080/ws'
        },
        hot: false
    }
});
