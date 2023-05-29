import 'chosen-js';

$.each($('.rs-form--chosen select, .rs-form--multi-chosen select'), function(index, el) {
  $(el).chosen($(el).data('settings'));
});