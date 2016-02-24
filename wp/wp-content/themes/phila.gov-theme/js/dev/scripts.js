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
  //prevent enter from refreshing the page and stopping filter search
  $('#filter-list input').keypress(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  $('.clickable-row').click(function() {
    window.document.location = $(this).data('href');
  });

  $('.clickable-row').hover(function() {
    $(this).addClass('is-hover');
    },
    function(){
      $(this).removeClass('is-hover');
  });

  var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
  if ( w <= 640 ){
    $('.all-services-info-list').addClass('equal-height');
  }
});
