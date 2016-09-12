jQuery(document).ready(function($) {

  $('.a-z-group .result').bind('update', function() {

    var parents = $('.a-z-group');

    // Loop through each parent element, finding only it's childeren
    parents.each(function(index, item) {

      var $item = $(item);
      var childElements = $item.find('.result.hidden');

      var total = $item.find('.result');

      if (childElements.length === total.length) {
          $item.hide();
      }
    });
  });


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
            $(this).hide().addClass('hidden');
          }else{
            $(this).show();
          }
        });

      }else{
        $('.result').show();
        $('#all').prop('checked', true);
      }

    });

    $('.a-z-group .result').trigger('update');
  });
});
