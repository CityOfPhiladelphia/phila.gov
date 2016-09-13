jQuery(document).ready(function($) {

  var parents = $('.a-z-group');
  var hiddenLetter = {};

  $('.a-z-group .result').bind('update', function() {

    parents.each(function(index, item) {

      var $item = $(item);
      var childElements = $item.find('.result.is-hidden');

      var total = $item.find('.result');

      if (childElements.length === total.length) {
        $item.hide();
        hiddenLetter[$item.data('alphabet')] =  $item.data('alphabet') ;
      }else{
        $item.show();
        delete hiddenLetter[$item.data('alphabet')];

      }
    });

      $('.a-z-list nav li').each( function ( i, v ) {
        $(hiddenLetter).each(function( index, value) {
          if ($(v).data('alphabet') == value) {
            $(v).addClass('ghost-gray is-disabled');
          }
        });

    });

  });


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

/*
    var check = $(this);
    if (check.prop('checked')) {
      serviceType[check.attr('name')] = check.val();
    } else {
      delete serviceType[check.attr('name')];
    }


    $.each(serviceType, function (index, value){
      console.log(value);
      if (value != 'all'){
        $('#all').prop('checked', false);

        $('.result').each(function( ){
          if ( $(this).data('service') != value ){
            $(this).hide().addClass('is-hidden');
          }else{
            $(this).show().removeClass('is-hidden');
          }
        });

      }else if( value == 'all' ){
        $('#service_filter input:checkbox').not(this).attr('checked', false);
        $('.result').show().removeClass('is-hidden');
        $('#all').prop('checked', true);
        parents.show();
      }

    });

*/
