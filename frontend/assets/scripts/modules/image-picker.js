import '../components/image-picker';
$.each($('.rs-form--image-picker select'), function(index, el) {
    $(el).imagepicker($(el).data('settings'));
});