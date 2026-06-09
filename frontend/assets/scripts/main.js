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
   * 初始化 Tab 切换
   */
  $(container).find('.rs-tabs').each(function() {
    var tabRoot = $(this);
    var tabLinks = tabRoot.find('.rs-tabs-nav a[data-wprs-tab-target]');
    var tabPanes = tabRoot.find('.rs-tab-pane');

    if (tabLinks.length === 0 || tabPanes.length === 0) {
      return;
    }

    tabLinks.off('click.wprsTabs').on('click.wprsTabs', function(event) {
      event.preventDefault();

      var link = $(this);
      var targetId = link.data('wprs-tab-target');
      if (!targetId) {
        return;
      }

      tabLinks.removeClass('rs-is-active nav-tab-active').attr('aria-selected', 'false');
      link.addClass('rs-is-active nav-tab-active').attr('aria-selected', 'true');

      tabPanes.removeClass('rs-is-active');
      tabRoot.find('#' + targetId).addClass('rs-is-active');
    });

    if (tabLinks.filter('.rs-is-active').length === 0) {
      tabLinks.first().addClass('rs-is-active').attr('aria-selected', 'true');
    }

    if (tabPanes.filter('.rs-is-active').length === 0) {
      var firstTargetId = tabLinks.first().data('wprs-tab-target');
      if (firstTargetId) {
        tabRoot.find('#' + firstTargetId).addClass('rs-is-active');
      }
    }
  });

  /**
   * 初始化 Stepper 切换
   */
  $(container).find('.rs-stepper').each(function() {
    var stepRoot = $(this);
    var stepLinks = stepRoot.find('.rs-stepper-link[data-wprs-step-target]');
    var stepPanes = stepRoot.find('.rs-step-pane');

    if (stepLinks.length === 0 || stepPanes.length === 0) {
      return;
    }

    var activateStepByIndex = function(targetIndex) {
      if (targetIndex < 0 || targetIndex >= stepLinks.length) {
        return;
      }

      stepLinks.each(function(index) {
        var link = $(this);
        var parent = link.closest('.rs-stepper-nav-item');
        var badge = link.find('.rs-stepper-badge');

        link.removeClass('rs-is-active rs-is-completed');
        parent.removeClass('rs-is-active rs-is-completed');

        if (index < targetIndex) {
          link.addClass('rs-is-completed').attr('aria-selected', 'false');
          parent.addClass('rs-is-completed');
          badge.text('✓');
        } else if (index === targetIndex) {
          link.addClass('rs-is-active').attr('aria-selected', 'true');
          parent.addClass('rs-is-active');
          badge.text(index + 1);
        } else {
          link.attr('aria-selected', 'false');
          badge.text(index + 1);
        }
      });

      stepPanes.removeClass('rs-is-active');
      var activeTargetId = stepLinks.eq(targetIndex).data('wprs-step-target');
      if (activeTargetId) {
        stepRoot.find('#' + activeTargetId).addClass('rs-is-active');
      }
    };

    var initialIndex = stepLinks.index(stepLinks.filter('.rs-is-active').first());
    if (initialIndex < 0) {
      initialIndex = 0;
    }
    activateStepByIndex(initialIndex);

    stepLinks.off('click.wprsStepper').on('click.wprsStepper', function(event) {
      event.preventDefault();
      var index = stepLinks.index(this);
      activateStepByIndex(index);
    });

    stepRoot.find('[data-wprs-step-next]').off('click.wprsStepperNext').on('click.wprsStepperNext', function() {
      var index = parseInt($(this).attr('data-wprs-step-next'), 10);
      if (!isNaN(index)) {
        activateStepByIndex(index + 1);
      }
    });

    stepRoot.find('[data-wprs-step-prev]').off('click.wprsStepperPrev').on('click.wprsStepperPrev', function() {
      var index = parseInt($(this).attr('data-wprs-step-prev'), 10);
      if (!isNaN(index)) {
        activateStepByIndex(index - 1);
      }
    });
  });

  /**
   * 初始化 repeater 容器交互。
   */
  $(container).find('.rs-repeater').each(function() {
    var repeaterRoot = $(this);
    var repeaterPrefix = repeaterRoot.attr('data-rs-repeater-prefix');
    var maxItems = parseInt(repeaterRoot.attr('data-rs-repeater-max'), 10);

    var updateRepeaterIndexes = function() {
      repeaterRoot.find('.rs-repeater__row').each(function(index) {
        var row = $(this);
        var oldPrefix = row.attr('data-rs-repeater-row-prefix');
        var oldIdPrefix = row.attr('data-rs-repeater-row-id-prefix');
        var newPrefix = repeaterPrefix + '[' + index + ']';
        var newIdPrefix = (repeaterPrefix || '').replace(/\[/g, '-').replace(/\]/g, '').replace(/--+/g, '-') + '-' + index;

        row.attr('data-rs-repeater-row-name', index);
        row.attr('data-rs-repeater-row-prefix', newPrefix);
        row.attr('data-rs-repeater-row-id-prefix', newIdPrefix);
        row.find('[name]').each(function() {
          var field = $(this);
          var fieldName = field.attr('name');
          if (fieldName && oldPrefix) {
            field.attr('name', fieldName.replace(oldPrefix, newPrefix));
          }
        });

        row.find('[id]').each(function() {
          var field = $(this);
          var fieldId = field.attr('id');
          if (fieldId && oldIdPrefix) {
            field.attr('id', fieldId.replace(oldIdPrefix.replace(/\[/g, '-').replace(/\]/g, '').replace(/--+/g, '-'), newIdPrefix));
          }
        });

        row.find('label[for]').each(function() {
          var label = $(this);
          var fieldFor = label.attr('for');
          if (fieldFor && oldIdPrefix) {
            label.attr('for', fieldFor.replace(oldIdPrefix.replace(/\[/g, '-').replace(/\]/g, '').replace(/--+/g, '-'), newIdPrefix));
          }
        });
      });

      repeaterRoot.attr('data-rs-repeater-next-index', repeaterRoot.find('.rs-repeater__row').length);
    };

    repeaterRoot.off('click.wprsRepeaterAdd').on('click.wprsRepeaterAdd', '.rs-repeater__add', function(event) {
      event.preventDefault();

      var rows = repeaterRoot.find('.rs-repeater__row');
      if (!isNaN(maxItems) && maxItems > 0 && rows.length >= maxItems) {
        return false;
      }

      var newRow = rows.first().clone();
      newRow.find('input').each(function() {
        if (this.type === 'checkbox' || this.type === 'radio') {
          this.checked = false;
        } else {
          $(this).val('');
        }
      });
      newRow.find('textarea').val('');
      newRow.find('select').each(function() {
        this.selectedIndex = 0;
      });

      repeaterRoot.find('.rs-repeater__footer').before(newRow);
      updateRepeaterIndexes();
      initWidgets(newRow[0]);

      return false;
    });

    repeaterRoot.off('click.wprsRepeaterDuplicate').on('click.wprsRepeaterDuplicate', '.rs-repeater__duplicate', function(event) {
      event.preventDefault();

      var rows = repeaterRoot.find('.rs-repeater__row');
      if (!isNaN(maxItems) && maxItems > 0 && rows.length >= maxItems) {
        return false;
      }

      var sourceRow = $(this).closest('.rs-repeater__row');
      var newRow = sourceRow.clone();

      sourceRow.after(newRow);
      updateRepeaterIndexes();
      initWidgets(newRow[0]);

      return false;
    });

    repeaterRoot.off('click.wprsRepeaterRemove').on('click.wprsRepeaterRemove', '.rs-repeater__remove', function(event) {
      event.preventDefault();

      var rows = repeaterRoot.find('.rs-repeater__row');
      if (rows.length <= 1) {
        rows.first().find('input').each(function() {
          if (this.type === 'checkbox' || this.type === 'radio') {
            this.checked = false;
          } else {
            $(this).val('');
          }
        });
        rows.first().find('textarea').val('');
        rows.first().find('select').each(function() {
          this.selectedIndex = 0;
        });

        return false;
      }

      $(this).closest('.rs-repeater__row').remove();
      updateRepeaterIndexes();

      return false;
    });

    updateRepeaterIndexes();
  });

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
