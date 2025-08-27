'use strict';

import './components/conditionize';
import './components/submit';
import './components/nette-forms';

import {addQueryArgs} from '@wordpress/url';

var loadjs = require('loadjs');
var distPath = wenpriseFormSettings.staticPath;
var admin_url = wenpriseFormSettings.admin_url;
var includes_url = wenpriseFormSettings.includes_url;

var wprs_get_assets_file = function($file_path) {
  var manifest = wenpriseFormSettings.manifest;
  return manifest[$file_path];
};

/**
 * 初始化各种组件
 */
function initWidgets(container) {
  container = container || document;

  /**
   * 点击图形验证码切换新图像
   */
  $(container).find('.rs-form--captcha .rs-captcha__img').on('click', function() {
    $(this).attr('src', addQueryArgs($(this).attr('src'), {code: Math.random()}));
  });

  /**
   * 根据条件显示表单
   */
  $(container).find('form').conditionize({
    selector    : '[data-cond]',
    customToggle: function($item, show) {
      if (show) {
        $item.parents('.rs-form-group').show();
      } else {
        $item.parents('.rs-form-group').hide();
      }
    },
  });

  if ($(container).find('.rs-form--color-picker').length > 0) {
    loadjs([
      includes_url + 'js/jquery/ui/core.min.js',
      includes_url + 'js/jquery/ui/mouse.min.js',
      includes_url + 'js/jquery/ui/draggable.min.js',
      includes_url + 'js/jquery/ui/slider.min.js',
      includes_url + 'js/jquery/jquery.ui.touch-punch.js',
      admin_url + 'js/iris.min.js',
      admin_url + 'js/color-picker.min.js',
      admin_url + 'css/color-picker.min.css'], 'color-picker');

    loadjs.ready('color-picker', function() {
      $(container).find('.rs-form--color-picker input').each(function(index, el) {
        var picker = $(el);
        picker.iris($(el).data('settings'));
        picker.blur(function() {
          setTimeout(function() {
            if (!$(document.activeElement).closest('.iris-picker').length) {
              picker.iris('hide');
            } else {
              picker.focus();
            }
          }, 0);
        });
        picker.focus(function() {
          picker.iris('show');
        });
      });
    });
  }

  if ($(container).find('.rs-form--uploader').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/styles/ajax-uploader.css'), distPath + wprs_get_assets_file('/dist/scripts/ajax-uploader.js')], 'uploader');
  }

  if ($(container).find('.rs-form--sms').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/scripts/send-sms.js')], 'send-sms');
  }

  if ($(container).find('.rs-form--signature').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/scripts/jq-signature.js')], 'signature');
  }

  if ($(container).find('.rs-form--star-rating').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/styles/star-rating.css'), distPath + wprs_get_assets_file('/dist/scripts/star-rating.js')], 'star-rating');
  }

  if ($(container).find('.rs-form--image-picker').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/scripts/image-picker.js')], 'image-picker');
  }

  if ($(container).find('.rs-form--chosen, .rs-form--multi-chosen').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/styles/chosen.css'), distPath + wprs_get_assets_file('/dist/scripts/chosen-js.js')], 'chosen');
  }

  if ($(container).find('.rs-form--table-input').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/scripts/table-input.js')], 'table-input', {async: false});
  }

  if ($(container).find('.rs-form--daterangepicker, .rs-form--datepicker').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/styles/daterangepicker.css'), distPath + wprs_get_assets_file('/dist/scripts/daterangepicker.js')],
        'daterangepicker');
  }

  if ($(container).find('.rs-form--slider').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/styles/ion-rangeslider.css'), distPath + wprs_get_assets_file('/dist/scripts/ion-rangeslider.js')],
        'ion-rangeslider');
  }

  if ($(container).find('.rs-form--inquiry').length > 0 && window.Alpine === undefined) {
    loadjs([distPath + wprs_get_assets_file('/dist/scripts/alpinejs.js')], 'alpinejs');
  }

  if ($(container).find('.rs-form--birthday').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/scripts/combodate.js')], 'combodate');
  }

  if ($(container).find('.rs-form--autocomplete').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/scripts/autocomplete.js')], 'autocomplete');
  }

  if ($(container).find('.rs-form--chained').length > 0) {
    loadjs([distPath + wprs_get_assets_file('/dist/scripts/cxselect.js')], 'cxselect');
  }
}

// 初始页面跑一次
initWidgets(document);

// 监听 htmx
document.body.addEventListener('htmx:afterSwap', function(evt) {
  initWidgets(evt.detail.target);
});
