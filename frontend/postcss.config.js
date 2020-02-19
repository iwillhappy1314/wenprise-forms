var fs = require('fs');
var postcss = require('postcss');
var classPrfx = require('postcss-class-prefix');

/* eslint-disable global-require, import/no-extraneous-dependencies */
module.exports = {
  // You can add more plugins and other postcss config
  // For more info see
  // <https://github.com/postcss/postcss-loader#configuration>
  // There is no need to use cssnano, webpack takes care of it!
  plugins: [
    require('autoprefixer'),
    require('cssnano')({
      preset: 'default',
    }),
    classPrfx('rs-', {
      ignore: [
        /rs-/,
        /rating-/,
        /clear-/,
        /theme-/,
        /krajee-/,
        /filled-/,
        /empty-/,
        /is-heart/,
        /is-display-only/,
        /star/,
        /caption/,
        /label/,
        /ui-/,
        /js-/,
        /irs/,
        /state_/,
        /admin-/,
        /chosen-/,
        /search-/,
        /result-/,
        /autocomplete-/,
        /combodate/,
        'group-name',
        'group-result',
        'group-option',
        'type_last',
        'lt-ie9',
        'small',
        'active-result',
        'disabled-result',
        'highlighted',
        'no-results',
      ],
    }),
  ],
};
