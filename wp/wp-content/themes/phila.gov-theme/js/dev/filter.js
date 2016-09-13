jQuery(document).ready(function($) {

  var parents = $('.a-z-group');

  $('.a-z-group .result').bind('update', function() {

    parents.each(function(index, item) {

      var $item = $(item);
      var childElements = $item.find('.result.is-hidden');

      var total = $item.find('.result');

      if (childElements.length === total.length) {
          $item.hide();
      }else{
        $item.show();
      }
    });
  });


//  var serviceType = {};

  $("#service_filter :checkbox").click(function() {
    $('.result').hide().addClass('is-hidden');

    if ( $(this).val() == 'all' ){

      $('#service_filter :checkbox').each(function(){
        $(this).prop('checked', false);
      });
      parents.show();
      $(this).prop('checked', true);
      $('.result').show().removeClass('is-hidden');


    }else{
      $('#all').prop('checked', false);

    }

  $('#service_filter :checkbox:checked').each(function(e) {
    var serviceType = $(this).val();

      $('.result').each(function( index, value ){

        $('.result').filter(function() {
          var arr = $(this).data('service').toString().split(/,\s+/);
            return $.inArray( serviceType, arr ) != -1;
          }).show().removeClass('is-hidden');


      });

    });
    $('.a-z-group .result').trigger('update');

  });

});
