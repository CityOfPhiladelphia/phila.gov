$(function() {
  window.dataLayer = window.dataLayer || [];
  console.log(params)

  $('.clickable-row').click( function(){
    window.dataLayer.push({
      'event' : 'GAEvent',
      'eventCategory' : 'Content Download',
      'eventAction' : params.postTitle,
      'eventLabel' : $(this).text(),
    });

  });
});