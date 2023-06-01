import 'devbridge-autocomplete';

$.each($('.rs-form--autocomplete input'), function(index, el) {
  $(el).devbridgeAutocomplete($(el).data('settings'));
});