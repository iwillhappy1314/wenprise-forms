import AppendGrid from 'jquery.appendgrid/src';
$.each($('.rs-table-input'), function(index, el) {
  new AppendGrid($(el).data('settings'));
});