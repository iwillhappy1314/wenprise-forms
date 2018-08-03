import 'dm-file-uploader';


jQuery(document).ready(function ($) {

    /**
     * 初始化文件上传组件
     */
    $('.js-uploader').dmUploader({
        url            : $('.js-uploader .c-uploader__shadow').data('url'),
        type           : 'POST',
        dataType       : 'json',
        allowedTypes   : 'image/*',
        onUploadSuccess: function (id, data) {

            var multiple = $(this).find('input[name=js_input_shadow]').attr('multiple');

            if (typeof multiple === typeof undefined || multiple === false) {
                $(this).find('.c-uploader__text').hide();
                $(this).find('.c-uploader__or').hide();
                //$(this).find('label').hide();
            }
            $(this).find('.c-uploader__preview').empty().append('<input type="hidden" name="' + $(this).data('name') +
                '" value="' + data.id + '">');

            $(this).find('.c-uploader__thumb').empty().show().append('<div class="col-xs-6 col-md-3"><a href="#" class="thumbnail"><img src="' +
                data.thumb + '" alt="Thumbnail"></a></div>');
        },
    });

    /**
     * 删除缩略图
     */
    $('.js-uploader button.close').bind('click', function () {
        var value = $(this).data('value'),
            multiple = $(this).closest('.js-uploader').find('input[name=js_input_shadow]').attr('multiple');

        $(this).parent().hide();
        $('.c-uploader__preview input').attr('value', '');

        $(this).closest('.js-uploader').show();

        if (typeof multiple === typeof undefined || multiple === false) {
            $(this).closest('.js-uploader').find('.c-uploader__text').show();
            $(this).closest('.js-uploader').find('.c-uploader__or').show();
            $(this).closest('.js-uploader').find('label').show();
        }

    });

    /**
     * 单文件上传时，如果已有文件，移除上传组件
     */
    $('input[name=input_shadow]').each(function() {
        var multiple = $(this).attr('multiple'),
            thumbs = $(this).closest('.js-uploader').find('.c-uploader__preview').children().length;

        if ((typeof multiple === typeof undefined || multiple === false) && thumbs > 0) {
            $(this).closest('.js-uploader').find('.c-uploader__text').hide();
            $(this).closest('.js-uploader').find('.c-uploader__or').hide();
            $(this).parent().hide();
        }

    });

});