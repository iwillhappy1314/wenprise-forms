'use strict';

import './components/conditionize';
import './components/submit';
import './components/nette-forms';

var loadjs = require('loadjs');
var distPath = wenpriseFormSettings.staticPath;

if ($('.rs-form--uploader').length > 0) {
  loadjs([distPath + 'styles/ajax-uploader.css', distPath + 'scripts/ajax-uploader.js'], 'uploader');
}

if ($('.rs-signature--control').length > 0) {
  loadjs([distPath + 'scripts/jq-signature.js'], 'signature');
}

if ($('.rs-form--star-rating').length > 0) {
  loadjs([distPath + 'styles/star-rating.css', distPath + 'scripts/star-rating.js'], 'star-rating');
}

if ($('.rs-form--image-picker').length > 0) {
  loadjs([distPath + 'scripts/image-picker.js'], 'image-picker');
}

if ($('.rs-form--chosen').length > 0) {
  loadjs([distPath + 'styles/chosen.css', distPath + 'scripts/chosen-js.js'], 'chosen');
}

if ($('.rs-form--table-input').length > 0) {
  loadjs([distPath + 'scripts/table-input.js'], 'table-input', {async: false});
}

if ($('.rs-form--daterangepicker').length > 0) {
  loadjs([distPath + 'styles/daterangepicker.css', distPath + 'scripts/daterangepicker.js'], 'daterangepicker');
}

if ($('.rs-form--slider').length > 0) {
  loadjs([distPath + 'styles/ion-rangeslider.css', distPath + 'scripts/ion-rangeslider.js'], 'ion-rangeslider');
}

if ($('.rs-form--inquiry').length > 0) {
  loadjs([distPath + 'scripts/alpinejs.js'], 'alpinejs');
}

if ($('.rs-form--birthday').length > 0) {
  loadjs([distPath + 'scripts/combodate.js'], 'combodate');
}

if ($('.rs-form--autocomplete').length > 0) {
  loadjs([distPath + 'scripts/autocomplete.js'], 'autocomplete');
}

jQuery(document)
    .ready(function($) {
      $('form')
          .conditionize({
            selector    : '[data-cond]',
            customToggle: function($item, show) {
              if (show) {
                $item.parents('.rs-form-group')
                    .show();
              } else {
                $item.parents('.rs-form-group')
                    .hide();
              }
            },
          });
    });