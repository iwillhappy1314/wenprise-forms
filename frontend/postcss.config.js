var fs = require('fs');
var postcss = require('postcss');
var cssnano = require('cssnano');
var autoprefixer = require('autoprefixer');
var tailwindcss = require('tailwindcss');
var classPrfx = require('postcss-class-prefix');

module.exports = {
	// You can add more plugins and other postcss config
	// For more info see
	// <https://github.com/postcss/postcss-loader#configuration>
	// There is no need to use cssnano, webpack takes care of it!
	plugins: [
		require('tailwindcss'),
		require('autoprefixer')({
			preset: 'default',
		}),
		classPrfx('rs-', {
			ignore: [
				/rs-/,
				/rsf-/,
				/rating-/,
				/clear-/,
				/theme-/,
				/krajee-/,
				/filled-/,
				/empty-/,
				/^daterangepicker\.*/,
				/^drp\.*/,
				/^drop\.*/,
				/^auto-apply\.*/,
				/^opens\.*/,
				/^single\.*/,
				/^right\.*/,
				/^next\.*/,
				/^prev\.*/,
				/^week\.*/,
				/^off\.*/,
				/^end\.*/,
				/^year\.*/,
				/^month\.*/,
				/^ampm\.*/,
				/^hour\.*/,
				/^second\.*/,
				/^ltr\.*/,
				/^rtl\.*/,
				/^active\.*/,
				/^minute\.*/,
				/^disable\.*/,
				/^in-range\.*/,
				/^available\.*/,
				/^range\.*/,
				/^calendar\.*/,
				/^left\.*/,
				/^show\.*/,
				/^active\.*/,
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
