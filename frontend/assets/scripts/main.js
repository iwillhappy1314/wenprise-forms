jQuery(document).ready(function($) {
  $('form').conditionize({
    selector    : '[data-cond]',
    customToggle: function($item, show) {
      if (show) {
        $item.parents('.rs-form-group').show();
      } else {
        $item.parents('.rs-form-group').hide();
      }
    },
  });
});