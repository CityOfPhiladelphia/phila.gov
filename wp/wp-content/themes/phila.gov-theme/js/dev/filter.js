jQuery(document).ready(function($) {

  $('#all').click(function() {
    $('#service_filter input:checkbox').not(this).attr('checked', false);
    $('.result').each(function() {
      $(this).show();
    });
  });

  var serviceType = {};

  $('#service_filter input:checkbox').click(function(e) {
    var check = $(this);
    if (check.prop('checked')) {
      serviceType[check.attr('name')] = check.val();
    } else {
      delete serviceType[check.attr('name')];
    }

    $.each(serviceType, function (index, value){
      if (value != 'all'){
        $('#all').prop('checked', false);

        $('.result').filter(function( ){
          if ( $(this).data('service') == value ){
            $(this).hide();
          }else{
            $(this).show();
          }
        });

      }else{
        $('.result').show();
        $('#all').prop('checked', true);
      }

    });
  });
});
