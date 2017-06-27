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
    window.location = $(this).data('href');
  });

  $('.clickable-row').hover(function() {
      $(this).addClass('is-hover');
    },
    function(){
      $(this).removeClass('is-hover');
  });

  // Staff summary expand
  $('[data-toggle="data-staff-bio"]').click(function(e){
    e.preventDefault();
    $(this).parent().siblings().toggleClass('expandable');
    if($(this).html() === ' Expand + '){
      $(this).html(' Collapse - ');
    } else {
      $(this).html(' Expand + ');
    }
  });

  /* Async loaded feedback forms */
  $.fn.feedbackify = function( src ) {
    postscribe('#form-container', '<script  type="text/javascript" src="' + src + '"><\/script>');
    $('html,body').animate({
        scrollTop: $('.feedback').position().top - $('header .is-stuck').height()
      }, 700 );
    return false;
  };

  $("footer .feedback").on('click', function(){
    $(this).feedbackify('https://form.jotform.com/jsform/62765090493967');
  });

  $(".neighborhood-resources .feedback").on('click', function(){
    $(this).feedbackify('https://form.jotform.com/jsform/62516788470970');
  });

  function hideEmptyCols(table) {
    var rows = $("tr", table).length-1;
    var numCols = $("th", table).length;
    for ( var i=1; i<=numCols; i++ ) {
        if ( $("span:empty", $("td:nth-child(" + i + ")", table)).length == rows ) {
            $("td:nth-child(" + i + ")", table).hide(); //hide <td>'s
            $("th:nth-child(" + i + ")", table).hide(); //hide header <th>
        }
    }
  }

  hideEmptyCols('.staff');

});
