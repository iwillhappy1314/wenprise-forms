const pkg = require('./package.json');

module.exports = {
  // Project Identity
  appName            : 'wenpriseForms', // Unique name of your project
  type               : 'plugin', // Plugin or theme
  slug               : 'wenprise-forms', // Plugin or Theme slug, basically the directory name under `wp-content/<themes|plugins>`
  // Used to generate banners on top of compiled stuff
  bannerConfig       : {
    name         : 'wenpriseForms',
    author       : '',
    license      : 'UNLICENSED',
    link         : 'UNLICENSED',
    version      : pkg.version,
    copyrightText:
        'This software is released under the UNLICENSED License\nhttps://opensource.org/licenses/UNLICENSED',
    credit       : true,
  },
  // Files we need to compile, and where to put
  files              : [
    // If this has length === 1, then single compiler
    {
      name         : 'app',
      entry        : {
        // mention each non-interdependent files as entry points
        // The keys of the object will be used to generate filenames
        // The values can be string or Array of strings (string|string[])
        // But unlike webpack itself, it can not be anything else
        // <https://webpack.js.org/concepts/#entry>
        // You do not need to worry about file-size, because we would do
        // code splitting automatically. When using ES6 modules, forget
        // global namespace pollutions 😉
        main           : [
          './assets/scripts/main.js',
          './assets/scripts/modules/conditionize.js',
          './assets/scripts/modules/nette-forms.js',
          './assets/styles/main.scss',
        ],
        tableinput    : [
          './assets/scripts/modules/table-input.js',
          './assets/scripts/modules/bootstrap-button.js',
        ],
        rangeslider: [
          './assets/scripts/modules/ion-rangeslider.js',
          './assets/styles/ion-rangeslider.scss',
        ],
        moment         : [
          './assets/scripts/modules/moment.js',
        ],
        combodate      : [
          './assets/scripts/modules/combodate.js',
        ],
        uploader  : [
          './assets/scripts/modules/ajax-uploader.js',
          './assets/styles/ajax-uploader.scss',
        ],
        autocomplete   : [
          './assets/scripts/modules/autocomplete.js',
        ],
        chosen         : [
          './assets/scripts/modules/chosen-js.js',
          './assets/styles/chosen.scss',
        ],
        datepicker     : [
          './assets/styles/datepicker.scss',
        ],
        signature      : [
          './assets/scripts/modules/jq-signature.js',
        ],
      },
      // Extra webpack config to be passed directly
      webpackConfig: undefined,
    },
    // If has more length, then multi-compiler
  ],
  // Output path relative to the context directory
  // We need relative path here, else, we can not map to publicPath
  outputPath         : 'dist',
  // Project specific config
  // Needs react(jsx)?
  hasReact           : false,
  // Needs sass?
  hasSass            : true,
  // Needs less?
  hasLess            : false,
  // Needs flowtype?
  hasFlow            : false,
  // Externals
  // <https://webpack.js.org/configuration/externals/>
  externals          : {
    jquery: 'jQuery',
  },
  // Webpack Aliases
  // <https://webpack.js.org/configuration/resolve/#resolve-alias>
  alias              : undefined,
  // Show overlay on development
  errorOverlay       : true,
  // Auto optimization by webpack
  // Split all common chunks with default config
  // <https://webpack.js.org/plugins/split-chunks-plugin/#optimization-splitchunks>
  // Won't hurt because we use PHP to automate loading
  optimizeSplitChunks: false,
  // Usually PHP and other files to watch and reload when changed
  watch              : './inc|includes/**/*.php',
  // Files that you want to copy to your ultimate theme/plugin package
  // Supports glob matching from minimatch
  // @link <https://github.com/isaacs/minimatch#usage>
  packageFiles       : [
    'inc/**',
    'vendor/**',
    'dist/**',
    '*.php',
    '*.md',
    'readme.txt',
    'languages/**',
    'layouts/**',
    'LICENSE',
    '*.css',
  ],
  // Path to package directory, relative to the root
  packageDirPath     : 'package',
  dangerouslyAllowCleanPatternsOutsideProject: true,
};
