import 'dm-file-uploader';

(function($) {
  var close_icon = '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="12" height="12"><path d="M49.6 158.4l104-108.8 358.4 352 356.8-352 105.6 105.6-352 356.8 352 355.2-102.4 107.2L512 620.8 155.2 974.4l-105.6-105.6L406.4 512z" p-id="3640" fill="#ffffff"></path></svg>',

      file_icon  = '<svg style="" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="64" height="64"><defs><style type="text/css"></style></defs><path d="M768 426.666667H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333zM341.333333 324.266667h170.666667a17.066667 17.066667 0 1 0 0-34.133334H341.333333a17.066667 17.066667 0 1 0 0 34.133334zM768 563.2H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333zM768 699.733333H341.333333a17.066667 17.066667 0 1 0 0 34.133334h426.666667a17.066667 17.066667 0 1 0 0-34.133334zM768 836.266667H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333z" p-id="2458" fill="#666666"></path><path d="M836.266667 248.9344V0H102.4v938.666667h85.333333v85.333333h733.866667V334.267733l-85.333333-85.333333z m-153.6-105.335467l153.6 153.6L863.3344 324.266667H682.666667V143.598933zM136.533333 904.533333V34.133333h665.6v180.667734L672.6656 85.333333H187.733333v819.2H136.533333z m85.333334 85.333334V119.466667h426.666666v238.933333h238.933334v631.466667H221.866667z" p-id="2459" fill="#666666"></path></svg>';

  $.fn.wprsAjaxUploader = function() {
    var options  = this.data('settings'),
        el       = this,

        defaults = {
          url             : el.find('.rs-uploader__shadow').data('url'),
          type            : 'POST',
          dataType        : 'json',
          maxFileSize     : wenpriseFormSettings.upload_max_filesize * 1000000,
          auto            : true,
          queue           : false,
          extFilter       : el.data('extfilter'),
          multiple        : (el.data('multiple') === true),
          onBeforeUpload  : function() {
            el.find('.js-uploader-message').empty();
          },
          onDragEnter     : function() {
            el.addClass('active');
          },
          onDragLeave     : function() {
            el.removeClass('active');
          },
          onUploadSuccess : function(id, responsive) {
            if (responsive.success === true) {
              var name        = el.data('name'),
                  is_multiple = (el.data('multiple') === true),
                  button      = '<button type="button" class="rs-uploader__close" data-value=' + responsive.data.id + '>' + close_icon + '</button>',
                  thumb       = '<a target=_blank href="' + responsive.data.url + '" class="rs-uploader__preview-image"><img src="' + responsive.data.thumb + '" alt="Thumbnail"></a>' + '<div class="rs-uploader__preview-name">' + responsive.data.title + '</div>';

              if (!responsive.data.thumb) {
                thumb = '<div class="rs-uploader__preview-image">' + file_icon + '</div>' + '<div class="rs-uploader__preview-name">' + responsive.data.title + '</div>';
              }

              el.find('input:text').filter(function() {
                return this.value === '';
              }).remove();

              if (!is_multiple) {
                el.find('.rs-uploader__text').hide();
                el.find('.rs-uploader__button').hide();
                el.find('.rs-uploader__value').empty().append('<input type="hidden" name="' + name + '" value="' + responsive.data.id + '">');
                el.find('.rs-uploader__preview').empty().show().append('<div class="rs-uploader__thumbnail">' + button + thumb + '</div>');
              } else {
                el.find('.rs-uploader__value').append('<input type="hidden" name="' + name + '" value="' + responsive.data.id + '">');
                el.find('.rs-uploader__preview').show().append('<div class="rs-uploader__thumbnail">' + button + thumb + '</div>');
              }
            } else {
              el.find('.js-uploader-message').html(responsive.data);
            }

            $( document.body ).trigger( 'wprs-ajax-uploader-success' );
          },
          onUploadError   : function(id, xhr, status, errorThrown) {
            el.find('.js-uploader-message').html(wenpriseFormSettings.error);

            $( document.body ).trigger( 'wprs-ajax-uploader-error' );
          },
          onUploadComplete: function(id) {
            el.find('.js-progress').remove();
          },
          onUploadCanceled: function(id) {
            el.find('.js-uploader-message').html(wenpriseFormSettings.canceled);
          },
          onUploadProgress: function(id, percent) {
            el.find('.js-uploader-message').html($('<div class="js-progress">').css('width', percent + '%'));
          },
          onFileTypeError : function(file) {
            el.find('.js-uploader-message').html(wenpriseFormSettings.file_type_error);
          },
          onFileSizeError : function(file) {
            el.find('.js-uploader-message').html(wenpriseFormSettings.file_size_error);
          },
          onFileExtError  : function(file) {
            el.find('.js-uploader-message').html(wenpriseFormSettings.file_ext_error);
          },
        };

    var settings = $.extend({}, defaults, options);

    /**
     * 初始化文件上传组件
     */
    el.dmUploader(settings);

    /**
     * 删除缩略图
     */
    $('body').on('click', '.rs-uploader__close', function() {

      var value       = $(this).data('value'),
          uploader    = $(this).closest('.js-uploader'),
          is_multiple = (uploader.data('multiple') === true);

      // 移除值
      if (!is_multiple) {
        uploader.find('.rs-uploader__value input').attr('value', '');

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

      var uploader    = $(this).closest('.js-uploader'),
          is_multiple = (uploader.data('multiple') === true),
          thumbnails  = uploader.find('.rs-uploader__preview').children().length;

      if (!is_multiple && thumbnails > 0) {
        uploader.find('.rs-uploader__text').hide();
        $(this).parent().hide();
      }

    });
  };

  /**
   * WordPress Uploader
   */
  $('.rs-wp-uploader__button').on('click', function(e) {
    e.preventDefault();

    var wprs_wp_media_uploader,
        wprs_wp_media_target_input = $(this).next().attr('id'),
        uploader                   = $(this).closest('.rs-wp-uploader'),
        name                       = uploader.data('name'),
        is_multiple                = (uploader.data('multiple') === true);

    if (wprs_wp_media_uploader) {
      wprs_wp_media_uploader.open();
      return;
    }

    wprs_wp_media_uploader = wp.media.frames.file_frame = wp.media({
      title   : wenpriseFormSettings.choose_image,
      button  : {
        text: wenpriseFormSettings.insert_image,
      },
      multiple: is_multiple,
    });

    wprs_wp_media_uploader.on('select', function() {
      var target_input = $('#' + wprs_wp_media_target_input).parent(),
          attachments  = wprs_wp_media_uploader.state().get('selection').toJSON();

      attachments.forEach(function(attachment) {
        var button     = '<button type="button" class="rs-uploader__close rs-wp-uploader__close" data-value=' + attachment.id + '>' + close_icon + '</button>',
            thumb      = '<div class="rs-uploader__thumbnail">' + button + '<img src="' + attachment.url + '" alt="Thumbnail"></div>',
            el_preview = target_input.find('.rs-uploader__preview'),
            el_value   = target_input.find('.rs-uploader__value');

        target_input.find('input:text').remove();

        if (is_multiple) {
          el_value.append('<input type="hidden" name="' + name + '" value="' + attachment.id + '">');

          el_preview.append(thumb).show();
        } else {
          el_value.html('<input type="hidden" name="' + name + '" value="' + attachment.id + '">');

          el_preview.html(thumb).show();
        }
      });
    });

    /**
     * 删除缩略图
     */
    $('.rs-form--wp-uploader').on('click', 'button.rs-wp-uploader__close', function(el) {
      var value       = $(this).data('value'),
          wp_uploader = $('body').find('.rs-wp-uploader__field');

      // 移除值
      wp_uploader.find('input[value=' + value + ']').remove();

      // 移除缩略图
      $(this).parent().remove();
    });

    wprs_wp_media_uploader.open();
  });

  $.each($('.rs-form--uploader'), function(index, el) {
    $(this).find('.js-uploader').wprsAjaxUploader();
  });

})(jQuery);