import 'ion-rangeslider';

$.each($('.rs-form--slider input'), function(index, el) {
  $(el).ionRangeSlider($(el).data('settings'));
});