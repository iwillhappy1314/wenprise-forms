export default function(e){
  e.preventDefault();

  var form         = $(this),
      submitButton = form.find('input[type=submit]');
  //form_data = WP_User_Frontend.validateForm(form);

  if (form_data) {

    // send the request
    form.find('li.wpuf-submit').append('<span class="wpuf-loading"></span>');
    submitButton.attr('disabled', 'disabled').addClass('button-primary-disabled');

    $.post(wenpriseFormSettings.ajaxurl, form_data, function(res) {
      // var res = $.parseJSON(res);

      if (res.success) {

        // enable external plugins to use events
        $('body').trigger('wprs:forms:success', res);

        if (res.show_message == true) {
          form.before('<div class="wpuf-success">' + res.message + '</div>');
          form.slideUp('fast', function() {
            form.remove();
          });

          //focus
          $('html, body').animate({
            scrollTop: $('.wpuf-success').offset().top - 100,
          }, 'fast');

        } else {
          window.location = res.redirect_to;
        }

      } else {

        if (typeof res.type !== 'undefined' && res.type === 'login') {

          if (confirm(res.error)) {
            window.location = res.redirect_to;
          } else {
            submitButton.removeAttr('disabled');
            submitButton.removeClass('button-primary-disabled');
            form.find('span.wpuf-loading').remove();
          }

          return;
        } else {
          if (form.find('.g-recaptcha').length > 0) {
            grecaptcha.reset();
          }

          swal({
            html              : res.error,
            type              : 'warning',
            showCancelButton  : false,
            confirmButtonColor: '#d54e21',
            confirmButtonText : 'OK',
            cancelButtonClass : 'btn btn-danger',
          });

        }

        submitButton.removeAttr('disabled');
      }

      submitButton.removeClass('button-primary-disabled');
      form.find('span.wpuf-loading').remove();
    });
  }
}