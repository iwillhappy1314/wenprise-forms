import '../components/combodate';
$.each($('.rs-form--birthday input'), function(index, el) {
    $(el).combodate($(el).data('settings'));
});