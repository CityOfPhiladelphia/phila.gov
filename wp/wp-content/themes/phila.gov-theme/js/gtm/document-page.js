$(function() {
  window.dataLayer = window.dataLayer || [];

  $('.clickable-row').click( function(e){
    var name = $(this).children().children()[1];
    window.dataLayer.push({
      'event' : 'GAEvent',
      'eventCategory' : 'Content Download',
      'eventAction' : params.postTitle,
      'eventLabel' : $(name)[0].innerText,
    });
  });
});