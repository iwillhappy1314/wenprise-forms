import 'cxselect';

$.each($('.rs-form--chained .input-group'), function(index, el) {
    $(el).cxSelect($(el).data('settings'));
});