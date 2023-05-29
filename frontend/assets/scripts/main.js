'use strict';

import './components/conditionize';
import './components/submit';

var loadjs = require('loadjs');

if ($('.rs-form--uploader').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/ajax-uploader.js'], 'uploader');
  loadjs([wenpriseFormSettings.staticPath + 'styles/ajax-uploader.css'], 'uploader-css');
}

if ($('.rs-signature--control').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/jq-signature.js'], 'signature');
}

if ($('.rs-form--star-rating').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/star-rating.js'], 'star-rating');
  loadjs([wenpriseFormSettings.staticPath + 'styles/star-rating.css'], 'star-rating-css');
}

if ($('.rs-form--image-picker').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/image-picker.js'], 'image-picker');
}

if ($('.rs-form--chosen').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/chosen-js.js'], 'chosen');
  loadjs([wenpriseFormSettings.staticPath + 'styles/chosen.css'], 'chosen-css');
}

if ($('.rs-form--table-input').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/table-input.js'], 'table-input');
}

if ($('.rs-form--daterangepicker').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/daterangepicker.js'], 'daterangepicker');
  loadjs([wenpriseFormSettings.staticPath + 'styles/daterangepicker.css'], 'daterangepicker-css');
}

if ($('.rs-form--datepicker').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/datepicker-zh.js'], 'datepicker-zh');
  loadjs([wenpriseFormSettings.staticPath + 'styles/datepicker.css'], 'datepicker-zh-css');
}

if ($('.rs-form--slider').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/ion-rangeslider.js'], 'ion-rangeslider');
  loadjs([wenpriseFormSettings.staticPath + 'styles/ion-rangeslider.css'], 'ion-rangeslider-css');
}

if ($('.rs-form--inquiry').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/alpinejs.js'], 'alpinejs');
}

if ($('.rs-form--birthday').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/moment.js'], 'moment');
  loadjs([wenpriseFormSettings.staticPath + 'scripts/combodate.js'], 'combodate');
}

if ($('.rs-form--autocomplete').length > 0) {
  loadjs([wenpriseFormSettings.staticPath + 'scripts/autocomplete.js'], 'autocomplete');
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