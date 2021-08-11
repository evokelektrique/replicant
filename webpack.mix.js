const mix = require('laravel-mix');

mix.setPublicPath('dist')
   .js('resources/scripts.js', 'dist')
   .sass('resources/styles.scss', 'dist')
