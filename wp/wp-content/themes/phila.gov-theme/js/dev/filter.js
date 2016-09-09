jQuery(document).ready(function($) {
  $('#service_filter input:checkbox').click(function(e) {
    var activeEl = e.target.id;
    console.log(activeEl);
    if (activeEl != 'all-services'){
      $('#all-services').prop('checked', false);
      $('.result').each(function() {

        if (activeEl != $(this).data('service')){
          $(this).hide();
        }

      });
    }else{
      $('.result').show();

      $('#all-services').prop('checked', true);
    }
  });
});
