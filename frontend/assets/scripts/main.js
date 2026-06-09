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
 * 只注册一次 loadjs 资源，避免 repeater 动态新增时重复定义同名 bundle。
 */
function ensureBundle(paths, bundleId, options) {
  if (!loadjs.isDefined(bundleId)) {
    loadjs(paths, bundleId, options);
  }
}

/**
 * 为动态新增的 wp_editor 克隆 WordPress 预初始化配置。
 */
function cloneEditorPreInitConfig(sourceId, targetId) {
  if (!window.tinyMCEPreInit || !sourceId || !targetId || sourceId === targetId) {
    return;
  }

  if (window.tinyMCEPreInit.mceInit && window.tinyMCEPreInit.mceInit[sourceId] && !window.tinyMCEPreInit.mceInit[targetId]) {
    window.tinyMCEPreInit.mceInit[targetId] = $.extend(true, {}, window.tinyMCEPreInit.mceInit[sourceId]);
    window.tinyMCEPreInit.mceInit[targetId].selector = '#' + targetId;
    window.tinyMCEPreInit.mceInit[targetId].elements = targetId;
  }

  if (window.tinyMCEPreInit.qtInit && window.tinyMCEPreInit.qtInit[sourceId] && !window.tinyMCEPreInit.qtInit[targetId]) {
    window.tinyMCEPreInit.qtInit[targetId] = $.extend(true, {}, window.tinyMCEPreInit.qtInit[sourceId]);
    window.tinyMCEPreInit.qtInit[targetId].id = targetId;
  }
}

/**
 * 清理从已初始化编辑器复制出来的运行时 DOM，保留 wp_editor 输出的原生结构。
 */
function resetClonedNativeEditor(field) {
  var wrap = field.closest('.wp-editor-wrap');

  if (!wrap.length) {
    return;
  }

  wrap.find('.wp-editor-container > .mce-tinymce, .wp-editor-container > .tox-tinymce').remove();
  wrap.find('.wp-editor-container [id^="mceu_"]').remove();
  wrap.find('.quicktags-toolbar').empty().removeClass('hide-if-no-js');

  field.css({
    display: '',
    visibility: '',
  }).removeAttr('aria-hidden');

  if (!wrap.hasClass('tmce-active') && !wrap.hasClass('html-active')) {
    wrap.addClass('tmce-active');
  }
}

/**
 * 按 WordPress 原生 footer 初始化逻辑激活动态新增的 wp_editor。
 */
function initializeNativeEditor(fieldId) {
  if (!window.tinyMCEPreInit || !fieldId) {
    return false;
  }

  var wrap = $('#wp-' + fieldId + '-wrap');
  var mceInit = window.tinyMCEPreInit.mceInit && window.tinyMCEPreInit.mceInit[fieldId];
  var qtInit = window.tinyMCEPreInit.qtInit && window.tinyMCEPreInit.qtInit[fieldId];

  if (window.tinymce && mceInit && !window.tinymce.get(fieldId)) {
    if (
        !mceInit.wp_skip_init &&
        (wrap.hasClass('tmce-active') || !window.tinyMCEPreInit.qtInit || !window.tinyMCEPreInit.qtInit.hasOwnProperty(fieldId))
    ) {
      $(document).trigger('wp-before-tinymce-init', mceInit);
      window.tinymce.init(mceInit);
    }
  }

  if (window.quicktags && qtInit && (!window.QTags || !window.QTags.getInstance(fieldId))) {
    $(document).trigger('wp-before-quicktags-init', qtInit);
    window.quicktags(qtInit);
  }

  if (!window.wpActiveEditor) {
    window.wpActiveEditor = fieldId;
  }

  return true;
}

/**
 * 初始化各种组件
 */
function initWidgets(container) {
  container = container || document;

  var clearRepeaterRowValues = function(row) {
    row.find('input, textarea, select').each(function() {
      if (this.type === 'button' || this.type === 'submit' || this.type === 'image' || this.type === 'file') {
        return;
      }

      if (this.type === 'checkbox' || this.type === 'radio') {
        this.checked = false;
        return;
      }

      $(this).val('');
    });
  };

  var copyRepeaterRowValues = function(sourceRow, targetRow) {
    var sourceFields = sourceRow.find('input, textarea, select').filter(function() {
      return this.type !== 'button' && this.type !== 'submit' && this.type !== 'image' && this.type !== 'file';
    });
    var targetFields = targetRow.find('input, textarea, select').filter(function() {
      return this.type !== 'button' && this.type !== 'submit' && this.type !== 'image' && this.type !== 'file';
    });

    sourceFields.each(function(index) {
      var target = targetFields.get(index);
      if (!target) {
        return;
      }

      if (this.type === 'checkbox' || this.type === 'radio') {
        target.checked = this.checked;
        return;
      }

      $(target).val($(this).val());
    });
  };

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

    if (!repeaterRoot.data('wprsRepeaterTemplate')) {
      var firstRow = repeaterRoot.find('.rs-repeater__row').first();
      if (firstRow.length > 0) {
        repeaterRoot.data('wprsRepeaterTemplate', firstRow.prop('outerHTML'));
      }
    }

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
            if (field.is('textarea.wp-editor-area')) {
              field.attr('data-wprs-editor-source-id', fieldId);
            }
            field.attr('id', fieldId.replace(oldIdPrefix.replace(/\[/g, '-').replace(/\]/g, '').replace(/--+/g, '-'), newIdPrefix));
          }
        });

        row.find('[data-id]').each(function() {
          var field = $(this);
          var fieldDataId = field.attr('data-id');
          if (fieldDataId && oldIdPrefix) {
            field.attr('data-id', fieldDataId.replace(oldIdPrefix.replace(/\[/g, '-').replace(/\]/g, '').replace(/--+/g, '-'), newIdPrefix));
          }
        });

        row.find('[data-name]').each(function() {
          var field = $(this);
          var fieldDataName = field.attr('data-name');
          if (fieldDataName && oldPrefix) {
            field.attr('data-name', fieldDataName.replace(oldPrefix, newPrefix));
          }
        });

        row.find('[data-wp-editor-id]').each(function() {
          var field = $(this);
          var editorId = field.attr('data-wp-editor-id');
          if (editorId && oldIdPrefix) {
            field.attr('data-wp-editor-id', editorId.replace(oldIdPrefix.replace(/\[/g, '-').replace(/\]/g, '').replace(/--+/g, '-'), newIdPrefix));
          }
        });

        row.find('[data-wprs-editor-settings]').each(function() {
          var field = $(this);
          var settings = field.attr('data-wprs-editor-settings');
          if (!settings || !oldPrefix) {
            return;
          }

          try {
            var parsedSettings = JSON.parse(settings);
            if (parsedSettings && parsedSettings.textarea_name) {
              parsedSettings.textarea_name = parsedSettings.textarea_name.replace(oldPrefix, newPrefix);
              field.attr('data-wprs-editor-settings', JSON.stringify(parsedSettings));
            }
          } catch (error) {
            // Ignore malformed editor settings and keep current attribute.
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

      var templateHtml = repeaterRoot.data('wprsRepeaterTemplate');
      var newRow = templateHtml ? $(templateHtml) : rows.first().clone();
      clearRepeaterRowValues(newRow);

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
      var templateHtml = repeaterRoot.data('wprsRepeaterTemplate');
      var newRow = templateHtml ? $(templateHtml) : sourceRow.clone();
      copyRepeaterRowValues(sourceRow, newRow);

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
    ensureBundle([
      includes_url + 'js/jquery/ui/widget.min.js',
      includes_url + 'js/jquery/ui/core.min.js',
      includes_url + 'js/jquery/ui/mouse.min.js',
      includes_url + 'js/jquery/ui/draggable.min.js',
      includes_url + 'js/jquery/ui/slider.min.js',
      includes_url + 'js/jquery/jquery.ui.touch-punch.js',
      admin_url + 'js/iris.min.js',
      admin_url + 'js/color-picker.min.js',
      admin_url + 'css/color-picker.min.css'], 'color-picker', {async: false});

    loadjs.ready('color-picker', function() {
      $(container).find('.rs-form--color-picker input').each(function(index, el) {
        var picker = $(el);
        if (picker.data('wprsColorPickerInitialized')) {
          return;
        }
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
        picker.data('wprsColorPickerInitialized', true);
      });
    });
  }

  if ($(container).find('.rs-form--uploader').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/styles/ajax-uploader.css'), distPath + wprs_get_assets_file('/dist/scripts/ajax-uploader.js')], 'uploader');
    loadjs.ready('uploader', function() {
      $(container).find('.rs-form--uploader .js-uploader').each(function() {
        if (typeof $(this).wprsAjaxUploader === 'function') {
          $(this).wprsAjaxUploader();
        }
      });
    });
  }

  if ($(container).find('.rs-form--sms').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/scripts/send-sms.js')], 'send-sms');
  }

  if ($(container).find('.rs-form--signature').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/scripts/jq-signature.js')], 'signature');
    loadjs.ready('signature', function() {
      $(container).find('.rs-form--signature input').each(function(index, el) {
        var field = $(el);
        var pad = $('#js-' + field.data('id'));

        if (!pad.length || pad.data('wprsSignatureInitialized')) {
          return;
        }

        pad.jqSignature(field.data('settings'));
        pad.off('jq.signature.changed.wprsSignature').on('jq.signature.changed.wprsSignature', function() {
          field.val(pad.jqSignature('getDataURL'));
        });

        field.parents('.rs-form--signature').find('.rs-clear-signature')
            .off('click.wprsSignature')
            .on('click.wprsSignature', function() {
              pad.jqSignature('clearCanvas');
            });

        pad.data('wprsSignatureInitialized', true);
      });
    });
  }

  if ($(container).find('.rs-form--star-rating').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/styles/star-rating.css'), distPath + wprs_get_assets_file('/dist/scripts/star-rating.js')], 'star-rating');
    loadjs.ready('star-rating', function() {
      $(container).find('.rs-form--star-rating input').each(function(index, el) {
        var field = $(el);
        if (field.data('rating')) {
          return;
        }

        field.rating(field.data('settings'));
      });
    });
  }

  if ($(container).find('.rs-form--image-picker').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/scripts/image-picker.js')], 'image-picker');
    loadjs.ready('image-picker', function() {
      $(container).find('.rs-form--image-picker select').each(function(index, el) {
        var field = $(el);
        field.imagepicker(field.data('settings'));
      });
    });
  }

  if ($(container).find('.rs-form--chosen, .rs-form--multi-chosen').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/styles/chosen.css'), distPath + wprs_get_assets_file('/dist/scripts/chosen-js.js')], 'chosen');
    loadjs.ready('chosen', function() {
      $(container).find('.rs-form--chosen select, .rs-form--multi-chosen select').each(function(index, el) {
        var field = $(el);
        if (field.data('chosen')) {
          return;
        }

        field.chosen(field.data('settings'));
      });
    });
  }

  if ($(container).find('.rs-form--table-input').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/scripts/table-input.js')], 'table-input', {async: false});
  }

  if ($(container).find('.rs-form--daterangepicker, .rs-form--datepicker').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/styles/daterangepicker.css'), distPath + wprs_get_assets_file('/dist/scripts/daterangepicker.js')],
        'daterangepicker');
    loadjs.ready('daterangepicker', function() {
      $(container).find('.rs-form--datepicker input, .rs-form--daterangepicker input').each(function(index, el) {
        var field = $(el);
        if (field.data('daterangepicker')) {
          return;
        }

        field.daterangepicker(field.data('settings'));
      });
    });
  }

  if ($(container).find('.rs-form--slider').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/styles/ion-rangeslider.css'), distPath + wprs_get_assets_file('/dist/scripts/ion-rangeslider.js')],
        'ion-rangeslider');
    loadjs.ready('ion-rangeslider', function() {
      $(container).find('.rs-form--slider input').each(function(index, el) {
        var field = $(el);
        if (field.data('ionRangeSlider')) {
          return;
        }

        field.ionRangeSlider(field.data('settings'));
      });
    });
  }

  if ($(container).find('.rs-form--birthday').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/scripts/combodate.js')], 'combodate');
    loadjs.ready('combodate', function() {
      $(container).find('.rs-form--birthday input').each(function(index, el) {
        var field = $(el);
        if (field.data('combodate')) {
          return;
        }

        field.combodate(field.data('settings'));
      });
    });
  }

  if ($(container).find('.rs-form--autocomplete').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/scripts/autocomplete.js')], 'autocomplete');
    loadjs.ready('autocomplete', function() {
      $(container).find('.rs-form--autocomplete input').each(function(index, el) {
        var field = $(el);
        if (field.data('autocomplete')) {
          return;
        }

        var settings = field.data('settings') || {};
        field.devbridgeAutocomplete({
          ...settings,
          minChars: 2,
          deferRequestBy: 300,
        });
      });
    });
  }

  if ($(container).find('.rs-form--chained').length > 0) {
    ensureBundle([distPath + wprs_get_assets_file('/dist/scripts/cxselect.js')], 'cxselect');
    loadjs.ready('cxselect', function() {
      $(container).find('.rs-form--chained .input-group').each(function(index, el) {
        var field = $(el);
        if (field.data('wprsCxSelectInitialized')) {
          return;
        }

        field.cxSelect(field.data('settings'));
        field.data('wprsCxSelectInitialized', true);
      });
    });
  }

  if ($(container).find('.rs-form--wp_editor').length > 0) {
    $(container).find('.rs-form--wp_editor textarea.wp-editor-area').each(function(index, el) {
      var field = $(el);
      var fieldId = field.attr('id');
      var sourceId = field.attr('data-wprs-editor-source-id');

      if (!fieldId || field.data('wprsEditorInitialized')) {
        return;
      }

      if (sourceId && sourceId !== fieldId) {
        cloneEditorPreInitConfig(sourceId, fieldId);
        resetClonedNativeEditor(field);
      }

      if (window.tinymce && tinymce.get(fieldId)) {
        field.data('wprsEditorInitialized', true);
        return;
      }

      initializeNativeEditor(fieldId);
      field.removeAttr('data-wprs-editor-source-id');
      field.data('wprsEditorInitialized', true);
    });
  }
}

// 初始页面跑一次
initWidgets(document);

// 监听 htmx
document.body.addEventListener('htmx:afterSwap', function(evt) {
  initWidgets(evt.detail.target);
});
