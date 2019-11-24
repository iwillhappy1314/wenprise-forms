﻿import 'dm-file-uploader';

jQuery(document).ready(function($) {

  /**
   * 初始化文件上传组件
   */
  $('.js-uploader').dmUploader({
    url            : $('.js-uploader .rs-uploader__shadow').data('url'),
    type           : 'POST',
    dataType       : 'json',
    multiple       : ($(this).data('multiple') === true),
    onUploadSuccess: function(id, data) {

      var name = $(this).data('name'),
          is_multiple = ($(this).data('multiple') === true),
          button = '<button type=button class="close" data-value=' + data.id + '>x</button>',
          thumb = '<img src="' + data.thumb + '" alt="Thumbnail">';

      if (!data.thumb) {
        thumb = '<svg t="1540194811704" class="icon" style="" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2457" xmlns:xlink="http://www.w3.org/1999/xlink" width="128" height="128"><defs><style type="text/css"></style></defs><path d="M768 426.666667H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333zM341.333333 324.266667h170.666667a17.066667 17.066667 0 1 0 0-34.133334H341.333333a17.066667 17.066667 0 1 0 0 34.133334zM768 563.2H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333zM768 699.733333H341.333333a17.066667 17.066667 0 1 0 0 34.133334h426.666667a17.066667 17.066667 0 1 0 0-34.133334zM768 836.266667H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333z" p-id="2458" fill="#666666"></path><path d="M836.266667 248.9344V0H102.4v938.666667h85.333333v85.333333h733.866667V334.267733l-85.333333-85.333333z m-153.6-105.335467l153.6 153.6L863.3344 324.266667H682.666667V143.598933zM136.533333 904.533333V34.133333h665.6v180.667734L672.6656 85.333333H187.733333v819.2H136.533333z m85.333334 85.333334V119.466667h426.666666v238.933333h238.933334v631.466667H221.866667z" p-id="2459" fill="#666666"></path></svg><br>' +
            data.title;
      }

      $(this).
          find('input:text').
          filter(function() { return this.value === ''; }).
          remove();

      if (!is_multiple) {
        $(this).find('.rs-uploader__text').hide();
        $(this).find('.rs-uploader__button').hide();

        $(this).
            find('.rs-uploader__value').
            empty().
            append('<input type="hidden" name="' + name + '" value="' + data.id + '">');

        $(this).
            find('.rs-uploader__preview').
            empty().
            show().
            append('<div class="rs-uploader__thumbnail">' + button + thumb + '</div>');
      } else {

        $(this).
            find('.rs-uploader__value').
            append('<input type="hidden" name="' + name + '" value="' + data.id + '">');

        $(this).
            find('.rs-uploader__preview').
            show().
            append('<div class="rs-uploader__thumbnail">' + button + thumb + '</div>');
      }

    },
  });

  /**
   * 删除缩略图
   */
  $('.js-uploader button.close').live('click', function() {

    var value = $(this).data('value'),
        uploader = $(this).closest('.js-uploader'),
        is_multiple = (uploader.data('multiple') === true);

    // 移除值
    if (!is_multiple) {

      $('.rs-uploader__value input').attr('value', '');

      uploader.show();
      uploader.find('.rs-uploader__text').show();
      uploader.find('.rs-uploader__button').show();

    } else {

      uploader.find('input[value=' + value + ']').remove();

    }

    // 移除缩略图
    $(this).parent().remove();

  });

  /**
   * 单文件上传时，如果已有文件，移除上传组件
   */
  $('input[name=js-input-shadow]').each(function() {

    var uploader = $(this).closest('.js-uploader'),
        is_multiple = (uploader.data('multiple') === true),
        thumbnails = uploader.find('.rs-uploader__preview').children().length;

    if (!is_multiple && thumbnails > 0) {
      uploader.find('.rs-uploader__text').hide();
      $(this).parent().hide();
    }

  });

});
