jQuery(document).ready(function($) {
  //TODO: Abstract for use on other data-types
  var hiddenLetter = {};
  var parents = $('.a-z-group');
  var $input = $('input.search-field');

  //Hide/show sets of letter groups
  $('.a-z-group .result').on('update', function() {

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
    //Disable anchor links when no results present for letter group
    $('.a-z-list nav li a').each( function ( i, value ) {
      var el = $(value);
      if ( el.data('alphabet') in hiddenLetter ) {
        $(this).attr('disabled', 'disabled');
        $(this).attr('aria-disabled', true);
      }else{
        $(this).removeAttr('disabled');
        $(this).attr('aria-disabled', false);
      }
    });
  });

  $('#service-filter').submit(function( e ) {
    e.preventDefault();
  })

  $input.keyup(function() {
    searchFilter();
  });


  function searchFilter(){
    function regexEscape(str) {
      return str.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')
    }

    function reg(input) {
      var flags;
      flags = 'gmi';
      input = regexEscape(input);
      return new RegExp( input, flags);
    }

    var value = $input.val();

    if( $('#service_filter :checkbox:checked').length > 0 ){

      $(".list .result").not('is-hidden').each(function(i, v) {
        if ( $( this ).text().search( reg( value ) ) < 0 ) {
          $(this).hide().addClass('is-hidden')
        }
      })
    }else{
      $(".list .result").each(function(i, v) {
        var value = $input.val();
        if ($(this).text().search(reg(value)) > -1 || value == '') {
          $(this).show().removeClass('is-hidden')
        } else {
          $(this).hide().addClass('is-hidden')

        }
      })

    }

    if ( value == '' ){
      //Match array values with checked items
      $('#service_filter :checkbox:checked').each(function(e) {
        var serviceType = $(this).val();

          $('.result').each(function( index, value ){

            $('.result').filter(function() {
              var arr = $(this).data('service').toString().split(/,\s+/);
                return $.inArray( serviceType, arr ) != -1;
              }).show().removeClass('is-hidden');
          });
          $('.a-z-group .result').trigger('update');
      })
    }

    $('.a-z-list .result').trigger('update');

  }

  //Watch checkboxes
  $("#service_filter :checkbox").click(function() {

    $('.result').hide().addClass('is-hidden');

    if ( $(this).val() === 'all' ){

      $('#service_filter :checkbox').each(function(){
        $(this).prop('checked', false);
      });

      parents.show();

      $(this).prop('checked', true);
      $('.result').show().removeClass('is-hidden');
    }else{
      $('#all').prop('checked', false);
    }

    if( $('#service_filter :checkbox:checked').length > 0 ){

      //Match array values with checked items
      $('#service_filter :checkbox:checked').each(function(e) {
        var serviceType = $(this).val();

          $('.result').each(function( index, value ){

            $('.result').filter(function() {
              var arr = $(this).data('service').toString().split(/,\s+/);
                return $.inArray( serviceType, arr ) != -1;
              }).show().removeClass('is-hidden');
          });
          $('.a-z-group .result').trigger('update');

          searchFilter();

        });

    }else{

      $('#all').prop('checked', true);
      $('.result').show().removeClass('is-hidden');
      parents.show();
      $('.a-z-list .result').trigger('update');

    }
  });

  $("a.scrollTo").click(function(e){
    var link = $(this);

    var stickyHeight = $('.sticky-container').outerHeight();

    if ( $('#wpadminbar').length ){
      var wpadminbarHeight = $('#wpadminbar').outerHeight();
    } else {
      var wpadminbarHeight = 0;
    }

    $('html, body').animate({
      scrollTop:
        $( $(this).attr("href") ).offset().top-(stickyHeight + wpadminbarHeight)
    }, 1000, function(){

      var anchor = link.attr("href").substr(1);
      var el = document.getElementById(anchor);

      el.focus();

    });

    return false;

  });

});
