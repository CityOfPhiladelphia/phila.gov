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
  $(".clickable-row").click(function() {
      window.document.location = $(this).data("href");
  });
  var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
  if ( w <= 640 ){
    $('.all-services-info-list').addClass('equal-height');
  }
});
