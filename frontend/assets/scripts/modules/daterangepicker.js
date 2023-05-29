import 'daterangepicker';

$.each($('.rs-form--datepicker input, .rs-form--daterangepicker input'), function(index, el) {
  $(el).daterangepicker($(el).data('settings'));
});