import '../components/send-sms';
$.each($('.rs-form--sms input[type=button]'), function(index, el) {
    $(el).send_sms($(el).data('settings'));
});