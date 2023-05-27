let mix = require('laravel-mix');
let webpack = require('webpack');

require('laravel-mix-tailwind');
require('laravel-mix-versionhash');
require('laravel-mix-copy-watched');
require('mix-white-sass-icons');

mix.setPublicPath('./');

mix.webpackConfig({
  externals: {
    jquery: 'jQuery',
  },
  plugins  : [
    new webpack.ProvidePlugin({
      $              : 'jquery',
      jQuery         : 'jquery',
      'window.jQuery': 'jquery',
    })],
});

mix.sass('assets/styles/main.scss', 'dist/styles')
    .sass('assets/styles/ion-rangeslider.scss', 'dist/styles')
    .sass('assets/styles/ajax-uploader.scss', 'dist/styles')
    .sass('assets/styles/chosen.scss', 'dist/styles')
    .sass('assets/styles/daterangepicker.scss', 'dist/styles')
    .sass('assets/styles/star-rating.scss', 'dist/styles')
    .sass('assets/styles/datepicker.scss', 'dist/styles')
    .tailwind()
    .options({
      outputStyle: 'compressed',
      postCss    : [
        require('css-mqpacker'),
      ],
    });

mix.js('assets/scripts/main.js', 'dist/scripts')
    .js('assets/scripts/modules/alpinejs.js', 'dist/scripts')
    .js('assets/scripts/modules/conditionize.js', 'dist/scripts')
    .js('assets/scripts/modules/table-input.js', 'dist/scripts')
    .js('assets/scripts/modules/bootstrap-button.js', 'dist/scripts')
    .js('assets/scripts/modules/ion-rangeslider.js', 'dist/scripts')
    .js('assets/scripts/modules/moment.js', 'dist/scripts')
    .js('assets/scripts/modules/combodate.js', 'dist/scripts')
    .js('assets/scripts/modules/ajax-uploader.js', 'dist/scripts')
    .js('assets/scripts/modules/autocomplete.js', 'dist/scripts')
    .js('assets/scripts/modules/chosen-js.js', 'dist/scripts')
    .js('assets/scripts/modules/image-picker.js', 'dist/scripts')
    .js('assets/scripts/modules/daterangepicker.js', 'dist/scripts')
    .js('assets/scripts/modules/datepicker-zh.js', 'dist/scripts')
    .js('assets/scripts/modules/jq-signature.js', 'dist/scripts')
    .js('assets/scripts/modules/sweet-alert.js', 'dist/scripts')
    .js('assets/scripts/modules/star-rating.js', 'dist/scripts')
    .js('assets/scripts/modules/nette-forms.js', 'dist/scripts');

//mix.copyWatched('assets/images', 'dist/images').
//    copyWatched('assets/fonts', 'dist/fonts');

if (mix.inProduction()) {
  mix.versionHash();
} else {
  mix.sourceMaps();
  mix.webpackConfig({devtool: 'eval-cheap-source-map'});
}

mix.browserSync({
  proxy         : 'http://chipsgate.as',
  files         : [
    {
      match  : [
        './dist/**/*',
        '../**/*.php',
      ],
      options: {
        ignored: '*.txt',
      },
    },
  ],
  snippetOptions: {
    whitelist: ['/wp-admin/admin-ajax.php'],
    blacklist: ['/wp-admin/**'],
  },
});
