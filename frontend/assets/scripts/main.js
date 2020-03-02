jQuery(document).ready(function($) {
  $('form').conditionize({
    selector    : '[data-cond]',
    customToggle: function($item, show) {
      if (show) {
        $item.parent().parent().show();
      } else {
        $item.parent().parent().hide();
      }
    },
  });
});