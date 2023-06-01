import 'jq-signature';

$.each($('.rs-form--signature input'), function(index, el) {
  var _el = $(el),
      pad = $('#js-' + _el.data('id'));

  pad.jqSignature(_el.data('settings'));
  pad.on('jq.signature.changed', function() {
    _el.val(pad.jqSignature('getDataURL'));
  });

  _el.parents('.rs-form--signature').find('.rs-clear-signature').click(function() {
    pad.jqSignature('clearCanvas');
  });
});