jQuery(document).ready(function($) {
  //TODO: Abstract for use on other data-types
  var hiddenLetter = {};
  var parents = $('.a-z-group');
  var $input = $('#a-z-filter-list .search-field');

  $('.a-z-group .search-field').prop( 'disabled', false );

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
  });

  $('#a-z-filter-list').submit(function( e ) {
    e.preventDefault();
  })

  $input.keyup(function(event) {
    searchFilter(event);
    const key = event.key; // const {key} = event; ES6+
    if (key === "Backspace" || key === "Delete") {
      $('.a-z-group .result').trigger('update');
      return false;
    }
  });



  function searchFilter(event){
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

    $(".list .result").not('is-hidden').each(function(i, v) {
      if ( $( this ).text().search( reg( value ) ) < 0 ) {
        $(this).hide().addClass('is-hidden')
      }
    })

    //check if value is empty
    if ( !value ){
      parents.show();
      $('.result').show().removeClass('is-hidden');
    }

    if ( $(".a-z-group:visible").length === 0) {
      $('.not-found').show();
    }else{
      $('.not-found').hide();
    }
    $('.a-z-group .result').trigger('update');
  }

});
