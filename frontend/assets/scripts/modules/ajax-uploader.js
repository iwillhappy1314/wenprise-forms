import 'dm-file-uploader';

jQuery(document).ready(function($) {

  var uploader = $('.js-uploader'),
      name = uploader.data('name'),
      is_multiple = (uploader.data('multiple') === true);

  /**
   * 初始化文件上传组件
   */
  uploader.dmUploader({
    url            : $('.js-uploader .c-uploader__shadow').data('url'),
    type           : 'POST',
    dataType       : 'json',
    allowedTypes   : 'image/*',
    multiple       : is_multiple,
    onUploadSuccess: function(id, data) {

      var button = '<button type=button class="close" data-value=' +
          data.id + '>x</button>',
          thumb = '<img src="' + data.thumb + '" alt="Thumbnail">';

      $(this).
          find('input:text').
          filter(function() { return this.value === ''; }).
          remove();

      if (!is_multiple) {
        $(this).find('.c-uploader__text').hide();
        $(this).find('.c-uploader__button').hide();

        $(this).
            find('.c-uploader__value').
            empty().
            append(
                '<input type="hidden" name="' + name + '" value="' + data.id +
                '">');

        $(this).
            find('.c-uploader__preview').
            empty().
            show().
            append(
                '<div class="c-uploader__thumbnail">' + button + thumb +
                '</div>');
      } else {

        $(this).
            find('.c-uploader__value').
            append('<input type="hidden" name="' + name +
                '" value="' + data.id + '">');

        $(this).
            find('.c-uploader__preview').
            show().
            append(
                '<div class="c-uploader__thumbnail">' + button + thumb +
                '</div>');
      }

    },
  });

  /**
   * 删除缩略图
   */
  $('.js-uploader button.close').live('click', function() {

    var value = $(this).data('value');

    // 移除值
    if (!is_multiple) {

      $('.c-uploader__value input').attr('value', '');

      $(this).closest('.js-uploader').show();
      $(this).closest('.js-uploader').find('.c-uploader__text').show();
      $(this).closest('.js-uploader').find('.c-uploader__button').show();

    } else {

      $(this).
          closest('.js-uploader').
          find('input[value=' + value + ']').
          remove();

    }

    // 移除缩略图
    $(this).parent().remove();

  });

  /**
   * 单文件上传时，如果已有文件，移除上传组件
   */
  $('input[name=js-input-shadow]').each(function() {

    var thumbnails = $(this).
        closest('.js-uploader').
        find('.c-uploader__preview').
        children().length;

    if (!is_multiple && thumbnails > 0) {
      $(this).closest('.js-uploader').find('.c-uploader__text').hide();
      $(this).parent().hide();
    }

  });

});