import 'bootstrap-star-rating';
import AppendGrid from 'jquery.appendgrid/src';

(function($) {
  'use strict';
  $.fn.ratingThemes['krajee-svg'] = {
    filledStar : '<span class="krajee-icon krajee-icon-star"></span>',
    emptyStar  : '<span class="krajee-icon krajee-icon-star"></span>',
    clearButton: '<span class="krajee-icon-clear"></span>',
  };

  $.each($('.rs-form--star-rating input'), function(index, el) {
    $(el).rating($(el).data('settings'));
  });
})(window.jQuery);