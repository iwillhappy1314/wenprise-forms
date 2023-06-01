'use strict';

import './components/conditionize';
import './components/submit';
import './components/nette-forms';

var loadjs = require('loadjs');
var distPath = wenpriseFormSettings.staticPath;

var wprs_get_assets_file = function($file_path) {
  var manifest = wenpriseFormSettings.manifest;
  return manifest[$file_path];
};

if ($('.rs-form--uploader').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/styles/ajax-uploader.css'), distPath + wprs_get_assets_file('/dist/scripts/ajax-uploader.js')], 'uploader');
}

if ($('.rs-form--signature').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/scripts/jq-signature.js')], 'signature');
}

if ($('.rs-form--star-rating').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/styles/star-rating.css'), distPath + wprs_get_assets_file('/dist/scripts/star-rating.js')], 'star-rating');
}

if ($('.rs-form--image-picker').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/scripts/image-picker.js')], 'image-picker');
}

if ($('.rs-form--chosen').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/styles/chosen.css'), distPath + wprs_get_assets_file('/dist/scripts/chosen-js.js')], 'chosen');
}

if ($('.rs-form--table-input').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/scripts/table-input.js')], 'table-input', {async: false});
}

if ($('.rs-form--daterangepicker').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/styles/daterangepicker.css'), distPath + wprs_get_assets_file('/dist/scripts/daterangepicker.js')],
      'daterangepicker');
}

if ($('.rs-form--slider').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/styles/ion-rangeslider.css'), distPath + wprs_get_assets_file('/dist/scripts/ion-rangeslider.js')],
      'ion-rangeslider');
}

if ($('.rs-form--inquiry').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/scripts/alpinejs.js')], 'alpinejs');
}

if ($('.rs-form--birthday').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/scripts/combodate.js')], 'combodate');
}

if ($('.rs-form--autocomplete').length > 0) {
  loadjs([distPath + wprs_get_assets_file('/dist/scripts/autocomplete.js')], 'autocomplete');
}

jQuery(document).ready(function($) {
  $('form').conditionize({
    selector    : '[data-cond]',
    customToggle: function($item, show) {
      if (show) {
        $item.parents('.rs-form-group').show();
      } else {
        $item.parents('.rs-form-group').hide();
      }
    },
  });
});