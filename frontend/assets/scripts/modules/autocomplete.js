import 'devbridge-autocomplete';

$.each($('.rs-form--autocomplete input'), function(index, el) {
  const settings = $(el).data('settings') || {};

  $(el).devbridgeAutocomplete({
    ...settings,
    minChars: 2,
    deferRequestBy: 300
  });
});
