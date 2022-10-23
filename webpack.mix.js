const mix = require( 'laravel-mix' );

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

mix.js( 'resources/js/auth.js', 'public/js' )
	.js( 'resources/js/dashboard.js', 'public/js' )
	.js( 'resources/js/landing.js', 'public/js' )
	.sass( 'resources/scss/auth.scss', 'public/css' )
	.sass( 'resources/scss/dashboard.scss', 'public/css' )
	.sass( 'resources/scss/landing.scss', 'public/css' )
	.browserSync({ proxy: '127.0.0.1:8000' })
	.webpackConfig({
		resolve: {
			modules: [ 'node_modules' ],
			extensions: [ '.js' ],
			alias: {
				'~': __dirname + '/resources/js'
			}
		}
	});

if ( mix.inProduction() ) {
	mix.version();
}
