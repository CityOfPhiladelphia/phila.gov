/**
 *
 * Custom js for our theme
 *
**/

//department filter list
new List('filter-list', {
    valueNames: ['item', 'item-desc']
});

jQuery(document).ready(function($) {


  //add search focus on tap or click
  $('.search-icon').click(function() {

    var fadeOn = $( ".fade" ).is( ":visible" );

    $('.search-field').focus();

    if ( !fadeOn ){
      $('#page').prepend('<div class="fade"></div>');
    }

    $('.fade').click(function() {
      $('.fade').remove();
    });

  });


  var alphaAlertHeight = $("#alpha-alert").css( "height" );

  //push the custom image down, past the alpha-alert so it will not be cut/not displayed at the full height
  if (alphaAlertHeight){
    $('body.custom-background').css('background-position-y', alphaAlertHeight );
  }

  //force foudation menus to display horizontally on desktop and vertically when 'is-drilldown' is present ( aka, on mobile )
  $('.is-drilldown').find('ul').addClass('vertical');

  //prevent enter from refreshing the page and stopping filter search
  $("#filter-list input").keypress(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  $(".clickable-row").click(function() {
      window.document.location = $(this).data("href");
  });
  var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
  if ( w <= 640 ){
    $('.all-services-info-list').addClass('equal-height');
  }
});
